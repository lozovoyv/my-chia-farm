/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

import Toast from './toast';

const ToastPlugin = function (_options) {

    this.options = _options;

    this.containerElement = null;

    this.getContainer = () => {
        if(this.containerElement !== null) {

            return this.containerElement;
        }

        let container = document.getElementById('mcf-toast');
        if(container) {
            this.containerElement = container;

            return container
        }

        container = document.createElement('div');
        container.id = 'mcf-toast';
        container.className = 'z-40 fixed inset-0 flex flex-col-reverse items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-end sm:justify-end';
        document.body.appendChild(container);
        this.containerElement = container;

        return container
    };

    this._show = (message, delay = null, type = null) => {
        const container = this.getContainer();
        new Toast({
            container: container,
            message: message,
            showTime: delay,
            type: type,
        });
    };

    this.show = (message, delay) => {
        this._show(message, delay)
    };
    this.success = (message, delay = null) => {
        this._show(message, delay, 'success')
    };
    this.info = (message, delay = null) => {
        this._show(message, delay, 'info')
    };
    this.error = (message, delay = null) => {
        this._show(message, delay, 'error')
    };

    return this;
};

export default ToastPlugin;
