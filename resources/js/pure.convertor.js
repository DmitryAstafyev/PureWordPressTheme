(function () {
    "use strict";
    if (typeof window.pure !== "object") { window.pure = {}; }
    window.pure.convertor = {
        UTF8: {
            encode: function (s) {
                return unescape(encodeURIComponent(s));
            },
            decode: function (s) {
                return decodeURIComponent(escape(s));
            }
        },
        BASE64: {
            decode: function (s) {
                var e = {}, i, k, v = [], r = "", w = String.fromCharCode, z,
                    n = [[65, 91], [97, 123], [48, 58], [43, 44], [47, 48]],
                    b = 0, c, x, l = 0, o = 0, char, num;
                for (z in n) { for (i = n[z][0]; i < n[z][1]; i++) { v.push(w(i)); } }
                for (i = 0; i < 64; i++) { e[v[i]] = i; }
                if (s.length < 100) {
                    var stop = true;
                }
                for (i = 0; i < s.length; i += 72) {
                    o = s.substring(i, i + 72);
                    for (x = 0; x < o.length; x++) {
                        c = e[o.charAt(x)]; b = (b << 6) + c; l += 6;
                        while (l >= 8) {
                            char    = w((b >>> (l -= 8)) % 256);
                            num     = char.charCodeAt(0);
                            r       = (num !== 0 ? r + char : r);
                        }
                    }
                }
                return r;
            },
            encode: function (s) {
                var b64     = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
                    o1, o2, o3, h1, h2, h3, h4, r, bits, i = 0,
                    ac      = 0,
                    enc     = "",
                    tmp_arr = [];
                if (!s) {
                    return s;
                }
                do { // pack three octets into four hexets
                    o1 = s.charCodeAt(i++);
                    o2 = s.charCodeAt(i++);
                    o3 = s.charCodeAt(i++);
                    bits = o1 << 16 | o2 << 8 | o3;
                    h1 = bits >> 18 & 0x3f;
                    h2 = bits >> 12 & 0x3f;
                    h3 = bits >> 6 & 0x3f;
                    h4 = bits & 0x3f;
                    // use hexets to index into b64, and append result to encoded string
                    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
                } while (i < s.length);
                enc = tmp_arr.join('');
                r   = s.length % 3;
                return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
            }
        }
    };
}());