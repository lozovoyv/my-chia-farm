/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

import ToastPlugin from './js/toaster';

export default {
    install: (app, options) => {
        if(!options) {
            options = {};
        }

        app.config.globalProperties.$toast = new ToastPlugin(options);
    }
}
