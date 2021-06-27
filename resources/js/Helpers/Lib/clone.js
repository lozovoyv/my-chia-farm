/*
 *  This file is part of the MyChiaFarm project.
 *
 *    (c) Lozovoy Vyacheslav <lozovoyv@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

function clone(original) {
    let copy;

    // Handle not object types
    if (original === null || typeof original !== 'object') return original;

    // Handle Date
    if (original instanceof Date) {
        copy = new Date();
        copy.setTime(original.getTime());
        return copy;
    }

    // Handle Array
    if (original instanceof Array) {
        copy = [];
        for (let i = 0, len = original.length; i < len; i++) {
            copy[i] = clone(original[i]);
        }
        return copy;
    }

    // Handle Object
    if (original instanceof Object) {
        copy = {};
        for (let attr in original) {
            if (original.hasOwnProperty(attr)) copy[attr] = clone(original[attr]);
        }
        return copy;
    }
}

export default clone;
