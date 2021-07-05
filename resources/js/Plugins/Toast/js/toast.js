/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

const Toast = function (options) {

    this.options = {
        container: null,
        message: null,
        showTime: 0,
        type: null,
        inDuration: 300,
        outDuration: 375,
    };

    this.element = null;
    this.remains = 0;

    this.prepareOptions = (options) => {
        if (options.container) {
            this.options.container = options.container;
        }
        if (options.message) {
            this.options.message = options.message;
        }
        if (options.showTime !== null) {
            this.options.showTime = options.showTime;
        }
        if (options.type) {
            this.options.type = options.type;
        }
        if (options.inDuration !== 'undefined') {
            this.options.inDuration = options.inDuration;
        }
        if (options.outDuration !== 'undefined') {
            this.options.outDuration = options.outDuration;
        }
    };

    this.createToast = () => {
        const close = document.createElement("div");
        close.className = "absolute top-3 right-3";
        close.innerHTML = '<svg class="w-4 h-4 cursor-pointer text-gray-500 hover:text-gray-700 dark:hover:text-gray-400" xmlns="http://www.w3.org/2000/svg"' +
            ' viewBox="0 0 20 20" fill="currentColor">' +
            '<path fill-rule="evenodd"' +
            ' d="M 0.5468683,16.299691 6.8464951,10.000001 0.5468683,3.7004089 3.700374,0.54683812 l 6.299627,6.29969938 6.299624,-6.29969938 3.153507,3.15357078 -6.299626,6.2995921 6.299626,6.29969 -3.153507,3.153471 -6.299624,-6.299599 -6.299627,6.299599 z"\n' +
            ' clip-rule="evenodd"/></svg>';
        close.addEventListener('click', this.remove);

        this.element = document.createElement("div");

        this.element.className = "relative max-w-sm w-full shadow-lg rounded-md pointer-events-auto relative mb-4 overflow-hidden bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 p-4 pr-10 text-sm";
        this.element.style.minHeight = "3.1rem";

        this.element.classList.add('border');

        if (this.options.type) {
            switch (this.options.type) {
                case 'success':
                    this.element.classList.add('border-green-400', 'dark:border-green-600');
                    break;
                case 'info':
                    this.element.classList.add('border-yellow-400', 'dark:border-yellow-600');
                    break;
                case 'error':
                    this.element.classList.add('border-red-400', 'dark:border-red-600');
                    break;
            }
        }

        this.element.innerHTML = this.options.message;
        this.element.appendChild(close);

        this.options.container.appendChild(this.element);

        this.startTimer();

        this.show();

        return this;
    };

    this.show = function () {
        // showing add animation here
    };

    this.startTimer = () => {
        if (this.options.showTime !== 0) {
            this.remains = this.options.showTime;
            this.interval = setInterval(() => {
                this.remains -= 20;
                if (this.remains <= 0) {
                    this.remove();
                }
            }, 20);
        }
    };

    this.remove = () => {
        window.clearInterval(this.interval);
        // add closing animation
        this.unset();
    };

    this.unset = () => {
        this.element.remove();
    };

    this.prepareOptions(options);
    this.createToast();

    return this;
};

export default Toast;
