/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

function empty(value) {
    if (value === null) return true;
    const type = typeof (value);

    switch (type) {
        case 'undefined':
            return true;
        case 'number':
            return isNaN(value);
        case 'string':
            return value.trim() === '';
        case 'object':
            return Object.keys(value).length < 1;
        case 'function':
        case 'boolean':
            return false;
        default:
            return false;
    }
}

export default empty;
