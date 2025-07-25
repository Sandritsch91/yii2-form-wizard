"use strict";

/**
 * FormWizard
 * Native JS form wizard plugin for Bootstrap 5
 * If step by step validation is required, jQuery is needed
 */
class FormWizard {
    constructor(containerSelector, options = {}, callbacks = {}) {
        this.options = {
            tabSelector: null,
            finishSelector: '[data-formwizard="finish"]',
            nextSelector: '[data-formwizard="next"]',
            previousSelector: '[data-formwizard="previous"]',
            validateSteps: [],
            keepPosition: true
        };

        this.callbacks = {
            onNext: null,
            onPrevious: null,
            onFinish: null
        };

        Object.assign(this.options, options);
        Object.assign(this.callbacks, callbacks);
        this.container = document.querySelector(containerSelector);

        this.tabPreviousIndex = null;
        this.tabCurrentIndex = null;
        this.tabNextIndex = null;
        this.tabMaxIndex = null;

        this.messages = {};

        this.positions = {};
        this.scrollElement = null;
        if (this.options.keepPosition !== false) {
            this.scrollElement = this.options.keepPosition === true ? window : document.querySelector(this.options.keepPosition);
        }

        this._bootstrap();
    }

    /**
     * Returns the index of the element
     * @param element
     * @returns {number}
     */
    getIndex(element) {
        return [...element.parentNode.children].findIndex(c => c == element) + 1;
    }

    /**
     * Returns the current index
     * @returns {number}
     */
    getCurrentIndex() {
        return this.getIndex(this.container.querySelector(this.options.tabSelector + ' .nav-item a.active'));
    }

    /**
     * Returns the next index
     * @returns {number|null}
     */
    getNextIndex() {
        let nextIndexCandidate = this.tabCurrentIndex + 1;

        if (this.container.querySelector(this.options.tabSelector + ' .nav-item:nth-child(' + nextIndexCandidate + ') a') === null) {
            return null;
        }
        return nextIndexCandidate;
    }

    /**
     * Returns the previous index
     * @returns {number|null}
     */
    getPreviousIndex() {
        let nextIndexCandidate = this.tabCurrentIndex - 1;

        if (this.container.querySelector(this.options.tabSelector + ' .nav-item:nth-child(' + nextIndexCandidate + ') a') === null) {
            return null;
        }

        return nextIndexCandidate;
    }

    /**
     * Initializes the form wizard.
     *
     * Do not call this method directly
     * @private
     */
    _bootstrap() {
        this.tabCurrentIndex = this.getCurrentIndex();
        this.tabNextIndex = this.getNextIndex();
        this.tabMaxIndex = 1;                           // Max index user can go forwards

        if (this.container.querySelectorAll(this.options.tabSelector + ' .nav-item').length > 1) {
            this.container.querySelector(this.options.previousSelector).setAttribute('disabled', 'disabled');
            this.container.querySelector(this.options.finishSelector).classList.add('d-none');
        }

        this.container.querySelectorAll(this.options.tabSelector + ' .nav-item a:not(.active)').forEach((element) => {
            element.classList.add('disabled');
        });

        this._addEventBindings();
    }

    /**
     * Adds event bindings to the buttons and tabs.
     *
     * Do not call this method directly
     * @private
     */
    _addEventBindings() {
        // Buttons
        if (this.container.querySelectorAll(this.options.tabSelector + ' .nav-item').length > 1) {
            this.container.querySelector(this.options.previousSelector).addEventListener('click', () => this._previous());
            this.container.querySelector(this.options.nextSelector).addEventListener('click', () => this._next());
        }
        this.container.querySelector(this.options.finishSelector).addEventListener('click', () => this._finish());

        // Tabs
        this.container.querySelectorAll(this.options.tabSelector + ' .nav-item a').forEach((element) => {
            element.addEventListener('click', () => {
                this.tabCurrentIndex = this.getIndex(element.parentNode);
                this.tabPreviousIndex = this.getPreviousIndex();
                this.tabNextIndex = this.getNextIndex();
                this._updateButtons();
            });
        });

        // Step by step validation
        let _this = this;
        if (this.options.validateSteps.length > 0) {
            $(this.container).find('form').on('afterValidateAttribute', function(event, attribute, messages) {
                _this.messages[attribute.name] = messages;
            });
        }

        // Keep position
        if (this.scrollElement !== null) {
            this.scrollElement.addEventListener('scroll', (event) => {
                let position;
                if (this.options.keepPosition === true) {
                    position = event.target.scrollingElement.scrollTop;
                } else {
                    position = document.querySelector(this.options.keepPosition).scrollTop
                }
                this.positions[this.tabCurrentIndex] = position;
            });
        }
    }

    /**
     * Updates the state of the previous, next and finish buttons
     * @private
     */
    _updateButtons() {
        if (this.tabCurrentIndex > 1) {
            this.container.querySelector(this.options.previousSelector).removeAttribute('disabled');
        }

        if (this.tabNextIndex !== null) {
            this.container.querySelector(this.options.nextSelector).classList.remove('d-none');
            this.container.querySelector(this.options.finishSelector).classList.add('d-none');
        }

        if (this.tabNextIndex === null) {
            this.container.querySelector(this.options.nextSelector).classList.add('d-none');
            this.container.querySelector(this.options.finishSelector).classList.remove('d-none');
        }

        if (this.tabPreviousIndex === null) {
            this.container.querySelector(this.options.previousSelector).setAttribute('disabled', 'disabled');
        }
    }

    /**
     * Updates the state of the tabs
     * @private
     */
    _updateTabs() {
        this.container.querySelectorAll(this.options.tabSelector + ' .nav-item a').forEach((element) => {
            if (this.getIndex(element.parentNode) <= this.tabMaxIndex) {
                element.classList.remove('disabled');
            } else {
                element.classList.add('disabled');
            }
        });
    }

    /**
     * Go to the next tab
     * @return {Promise<boolean>}
     * @private
     */
    async _next() {
        if (this.callbacks.onNext !== null && await this.callbacks.onNext() === false) {
            return false;
        }

        if (this.options.validateSteps.length > 0) {
            let form = $(this.container).find('form');
            let data = form.yiiActiveForm('data');
            let step = this.options.validateSteps[this.tabCurrentIndex - 1];

            step.forEach((attribute) => {
                let element = data.attributes.find((item) => item.name === attribute);
                form.yiiActiveForm('validateAttribute', element.id);
            });

            while (Object.keys(this.messages).length < step.length) {
                await this._sleep(20);
            }

            let valid = true;
            for (let i = 0; i < step.length; i++) {
                valid = valid && this.messages[step[i]]?.length === 0;
            }

            this.messages = {};
            if (!valid) {
                return false;
            }
        }

        let nextElement = this.container.querySelector(this.options.tabSelector + ' .nav-item:nth-child(' + this.tabNextIndex + ') a');
        bootstrap.Tab.getOrCreateInstance(nextElement).show();

        this.tabCurrentIndex = this.tabNextIndex;
        this.tabPreviousIndex = this.getPreviousIndex();
        this.tabNextIndex = this.getNextIndex();

        this.tabMaxIndex = this.tabMaxIndex < this.tabCurrentIndex ? this.tabCurrentIndex : this.tabMaxIndex;

        if (this.options.keepPosition !== false && Object.keys(this.positions).includes(this.tabCurrentIndex.toString())) {
            this.scrollElement.scrollTo({
                top: this.positions[this.tabCurrentIndex],
                behavior: 'instant'
            });
        }

        this._updateButtons();
        this._updateTabs();
    }

    /**
     * Go to the previous tab
     * @return {Promise<boolean>}
     * @private
     */
    async _previous() {
        if (this.callbacks.onPrevious !== null && await this.callbacks.onPrevious() === false) {
            return false;
        }

        let nextElement = this.container.querySelector(this.options.tabSelector + ' .nav-item:nth-child(' + this.tabPreviousIndex + ') a');
        bootstrap.Tab.getOrCreateInstance(nextElement).show();

        this.tabCurrentIndex = this.tabPreviousIndex;
        this.tabPreviousIndex = this.getPreviousIndex();
        this.tabNextIndex = this.getNextIndex();

        if (this.options.keepPosition !== false && Object.keys(this.positions).includes(this.tabCurrentIndex.toString())) {
            this.scrollElement.scrollTo({
                top: this.positions[this.tabCurrentIndex],
                behavior: 'instant',
            });
        }

        this._updateButtons();
        this._updateTabs();
    }

    /**
     * Finish the form wizard
     * @return {Promise<boolean>}
     * @private
     */
    async _finish() {
        return !(this.callbacks.onFinish !== null && await this.callbacks.onFinish() === false);
    }

    /**
     * Sleep function
     * @param ms
     * @return {Promise<unknown>}
     * @private
     */
    _sleep = function(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}
