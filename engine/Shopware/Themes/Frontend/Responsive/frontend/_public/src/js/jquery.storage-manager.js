;(function (window, document) {
    'use strict';

    /**
     * Global storage manager
     *
     * The storage manager provides a unified way to store items in the localStorage and sessionStorage.
     * It uses a polyfill that uses cookies as a fallback when no localStorage or sessionStore is available or working.
     *
     * @example
     *
     * Saving an item to localStorage:
     *
     * StorageManager.setItem('local', 'key', 'value');
     *
     * Retrieving it:
     *
     * var item = StorageManager.getItem('local', 'key'); // item === 'value'
     *
     * Basically you can use every method of the Storage interface (http://www.w3.org/TR/webstorage/#the-storage-interface)
     * But notice that you have to pass the storage type ('local' | 'session') in the first parameter for every call.
     *
     * @example
     *
     * Getting the localStorage/sessionStorage (polyfill) object
     *
     * var localStorage = StorageManager.getStorage('local');
     * var sessionStorage = StorageManager.getStorage('session');
     *
     * You can also use its shorthands:
     *
     * var localStorage = StorageManager.getLocalStorage();
     * var sessionStorage = StorageManager.getSessionStorage();
     */
    window.StorageManager = (function () {

        /**
         * The polyfill for localStorage and sessionStorage.
         * Uses cookies for storing items.
         *
         * @class StoragePolyFill
         * @constructor
         * @param {String} type
         * @returns {Object}
         */
        function StoragePolyFill(type) {
            /**
             * Creates a cookie with a given name, its values as a string (e.g. JSON) and expiration in days
             *
             * @param {String} name
             * @param {String} value
             * @param {Number} days
             */
            function createCookie(name, value, days) {
                var date,
                    expires = '';

                if (days) {
                    date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = '; expires=' + date.toGMTString();
                }

                value = encodeURI(value);

                document.cookie = name + '=' + value + expires + '; path=/';
            }

            /**
             * Searches for a cookie by the given name and returns its values.
             *
             * @param name
             * @returns {String|null}
             */
            function readCookie(name) {
                var nameEq = name + '=',
                    cookies = document.cookie.split(';'),
                    cookie,
                    len = cookies.length,
                    i = 0;

                for (; i < len; i++) {
                    cookie = cookies[i];

                    while (cookie.charAt(0) == ' ') {
                        cookie = cookie.substring(1, cookie.length);
                    }

                    if (cookie.indexOf(nameEq) == 0) {
                        return decodeURI(cookie.substring(nameEq.length, cookie.length));
                    }
                }
                return null;
            }

            /**
             * Turns the passed data object into a string via JSON.stringify() and sets it into its proper cookie.
             *
             * @param {Object} data
             */
            function setData(data) {
                data = JSON.stringify(data);
                if (type == 'session') {
                    createCookie('sessionStorage', data, 0);
                } else {
                    createCookie('localStorage', data, 365);
                }
            }

            /**
             * clears the whole data set of a storage cookie.
             */
            function clearData() {
                if (type == 'session') {
                    createCookie('sessionStorage', '', 0);
                } else {
                    createCookie('localStorage', '', 365);
                }
            }

            /**
             * Returns the data set of a storage cookie.
             *
             * @returns {Object}
             */
            function getData() {
                var data = (type == 'session') ? readCookie('sessionStorage') : readCookie('localStorage');

                return data ? JSON.parse(data) : { };
            }

            var data = getData();

            /**
             * Returns an object to expose public functions and hides privates.
             */
            return {
                /**
                 * data set length.
                 *
                 * @public
                 * @property length
                 * @type {Number}
                 */
                length: 0,

                /**
                 * Clears the whole data set.
                 *
                 * @public
                 * @method clear
                 */
                clear: function () {
                    var me = this,
                        p;

                    for(p in data) {
                        if (!data.hasOwnProperty(p)) {
                            continue;
                        }
                        delete data[p];
                    }

                    me.length = 0;

                    clearData();
                },

                /**
                 * Returns the data item by the given key or null if the item was not found.
                 *
                 * @param key
                 * @returns {String|null}
                 */
                getItem: function (key) {
                    return typeof data[key] === 'undefined' ? null : data[key];
                },

                /**
                 * Returns the data item key of the given index.
                 *
                 * @param {Number} index
                 * @returns {String}
                 */
                key: function (index) {
                    var i = 0,
                        p;

                    for (p in data) {
                        if (!data.hasOwnProperty(p)) {
                            continue;
                        }

                        if (i === index) {
                            return p;
                        }

                        i++;
                    }

                    return null;
                },

                /**
                 * Removes an item by the given key.
                 *
                 * @param {String} key
                 */
                removeItem: function (key) {
                    var me = this;

                    if (data.hasOwnProperty(key)) {
                        me.length--;
                    }

                    delete data[key];

                    setData(data);
                },

                /**
                 * Sets the value of a storage item.
                 *
                 * @param {String} key
                 * @param {String} value
                 */
                setItem: function (key, value) {
                    var me = this;

                    if (!data.hasOwnProperty(key)) {
                        me.length++;
                    }

                    data[key] = value + ''; // forces the value to a string

                    setData(data);
                }
            };
        }

        var localStorageSupported = (typeof window.localStorage !== 'undefined'),
            sessionStorageSupported = (typeof window.sessionStorage !== 'undefined'),
            storage = {
                local: localStorageSupported ? window.localStorage : new StoragePolyFill('local'),
                session: sessionStorageSupported ? window.sessionStorage : new StoragePolyFill('session')
            },
            p;

        // test for safari's "QUOTA_EXCEEDED_ERR: DOM Exception 22" issue.
        for (p in storage) {
            if (!storage.hasOwnProperty(p)) {
                continue;
            }

            try {
                storage[p].setItem('storage', '');
                storage[p].removeItem('storage');
            }
            catch (err) {
                storage[p] = new StoragePolyFill(p);
            }
        }

        // Just return the public API instead of all available functions
        return {
            /**
             * Returns the storage object/polyfill of the given type.
             *
             * @returns {Storage|StoragePolyFill}
             */
            getStorage: function (type) {
                return storage[type];
            },

            /**
             * Returns the sessionStorage object/polyfill.
             *
             * @returns {Storage|StoragePolyFill}
             */
            getSessionStorage: function () {
                return this.getStorage('session');
            },

            /**
             * Returns the localStorage object/polyfill.
             *
             * @returns {Storage|StoragePolyFill}
             */
            getLocalStorage: function () {
                return this.getStorage('local');
            },

            /**
             * Calls the clear() method of the storage from the given type.
             *
             * @param {String} type
             */
            clear: function (type) {
                this.getStorage(type).clear();
            },

            /**
             * Calls the getItem() method of the storage from the given type.
             *
             * @param {String} type
             * @param {String} key
             * @returns {String}
             */
            getItem: function (type, key) {
                return this.getStorage(type).getItem(key);
            },

            /**
             * Calls the key() method of the storage from the given type.
             *
             * @param {String} type
             * @param {Number|String} i
             * @returns {String}
             */
            key: function (type, i) {
                return this.getStorage(type).key(i);
            },

            /**
             * Calls the removeItem() method of the storage from the given type.
             *
             * @param {String} type
             * @param {String} key
             */
            removeItem: function (type, key) {
                this.getStorage(type).removeItem(key);
            },

            /**
             * Calls the setItem() method of the storage from the given type.
             *
             * @param {String} type
             * @param {String} key
             * @param {String} value
             */
            setItem: function (type, key, value) {
                this.getStorage(type).setItem(key, value);
            }
        };
    })();
})(window, document);