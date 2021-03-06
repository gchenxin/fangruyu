define(function(require) {
    function t(t, s, a) {
        if (this.widget = t, this._defaults = e, this._name = i, this.widget === !0) this.element = this.container(s);
        else {
            this.element = this.widget.find("." + i);
            var s = this.element.parent(),
            a = [];
            $.each(s.find(".slidesjs-slide"),
            function(t, i) {
                var e = $(i),
                s = {},
                n = e.find("img");
                s.src = n.attr("src"),
                s.url = e.find(".thumblink").attr("href"),
                s.alt = n.attr("alt"),
                a.push(s)
            }),
            this.element.empty().removeAttr("style")
        }
        var n = {
            width: s.attr("data-width"),
            height: s.attr("data-height"),
            play: {
                auto: !0
            }
        };
        this.options = $.extend(!0, {},
        e, n),
        this.element.css({
            width: this.options.width,
            height: this.options.height
        }),
        this.addItems(a),
        this.init()
    }
    var $ = require("jquery"),
    i = "slidesjs",
    e = {
        width: 940,
        height: 528,
        start: 1,
        navigation: {
            active: !0,
            effect: "slide"
        },
        pagination: {
            active: !0,
            effect: "slide"
        },
        play: {
            active: !1,
            effect: "slide",
            interval: 5e3,
            auto: !1,
            swap: !0,
            pauseOnHover: !1,
            restartDelay: 2500
        },
        effect: {
            slide: {
                speed: 500
            },
            fade: {
                speed: 300,
                crossfade: !0
            }
        },
        callback: {
            loaded: function() {},
            start: function() {},
            complete: function() {}
        }
    };
    return t.prototype.container = function(t) {
        return $('<div class="' + i + '"></div>').appendTo(t)
    },
    t.prototype.addItems = function(t) {
        var i = this.element;
        $.each(t, $.proxy(function(t, e) {
            var s = '<div><a class="thumblink" href="' + (e.url || "javascript:void(0)") + '" ><img src="' + e.src + '" alt="' + e.alt + '" /></a></div>',
            a = $(s);
            i.append(a),
            a.find("img").css("display", "none").load(function(t) {
                var i = $(t.target),
                e = i.parents(".slidesjs-slide");
                e.addClass("sliderimgLoaded"),
                i.fadeIn(),
                "block" == e.css("display") ? e.attr("_visible", !0) : e.show(),
                i.css({
                    marginTop: (e.height() - i.height()) / 2
                }),
                "true" != e.attr("_visible") && e.hide()
            })
        },
        this))
    },
    t.prototype.title = function(t, i) {
        var e = $('<a class="title" href="' + i.url + '">' + i.alt + "</a>");
        e.appendTo(t),
        t.prev().length > 0 && e.fadeOut()
    },
    t.prototype.init = function() {
        var t = this.options.width,
        i = this.options.height;
        "100%" == t && (t = this.element.width());
        var e, s, a, n, o, d, r = this;
        return e = this.element,
        this.data = $.data(this),
        $.data(this, "animating", !1),
        $.data(this, "total", e.children().not(".slidesjs-navigation", e).length),
        $.data(this, "current", this.options.start - 1),
        "undefined" != typeof TouchEvent && ($.data(this, "touch", !0), this.options.effect.slide.speed = this.options.effect.slide.speed / 2),
        e.css({
            overflow: "hidden"
        }),
        e.slidesContainer = e.children().not(".slidesjs-navigation", e).wrapAll("<div class='slidesjs-container'>", e).parent().css({
            overflow: "hidden",
            position: "relative"
        }),
        $(".slidesjs-container", e).wrapInner("<div class='slidesjs-control'>", e).children(),
        $(".slidesjs-control", e).css({
            position: "relative",
            left: 0
        }),
        $(".slidesjs-control", e).children().addClass("slidesjs-slide").css({
            position: "absolute",
            top: 0,
            left: 0,
            width: "100%",
            height: this.options.height,
            zIndex: 0,
            display: "none"
        }),
        $.each($(".slidesjs-control", e).children(),
        function(t) {
            var i;
            return i = $(this),
            i.attr("slidesjs-index", t)
        }),
        this.data.touch && ($(".slidesjs-control", e).on("touchstart",
        function(t) {
            return r._touchstart(t)
        }), $(".slidesjs-control", e).on("touchmove",
        function(t) {
            return r._touchmove(t)
        }), $(".slidesjs-control", e).on("touchend",
        function(t) {
            return r._touchend(t)
        })),
        e.fadeIn(0),
        this.update(),
        this.data.touch && this._setuptouch(),
        $(".slidesjs-control", e).children(":eq(" + this.data.current + ")").eq(0).fadeIn(0,
        function() {
            return $(this).css({
                zIndex: 10
            })
        }),
        this.options.navigation.active && (o = $("<a>", {
            "class": "slidesjs-previous slidesjs-navigation",
            href: "#",
            title: "Previous",
            text: "Previous"
        }).appendTo(e), s = $("<a>", {
            "class": "slidesjs-next slidesjs-navigation",
            href: "#",
            title: "Next",
            text: "Next"
        }).appendTo(e), setTimeout(function() {
            var t = (i - o.outerHeight()) / 2;
            o.css("top", t),
            s.css("top", t)
        },
        100)),
        $(".slidesjs-next", e).click(function(t) {
            return t.preventDefault(),
            r.next(r.options.navigation.effect)
        }),
        $(".slidesjs-previous", e).click(function(t) {
            return t.preventDefault(),
            r.previous(r.options.navigation.effect)
        }),
        this.options.play.active && (n = $("<a>", {
            "class": "slidesjs-play slidesjs-navigation",
            href: "#",
            title: "Play",
            text: "Play"
        }).appendTo(e), d = $("<a>", {
            "class": "slidesjs-stop slidesjs-navigation",
            href: "#",
            title: "Stop",
            text: "Stop"
        }).appendTo(e), n.click(function(t) {
            return t.preventDefault(),
            r.play(!0)
        }), d.click(function(t) {
            return t.preventDefault(),
            r.stop(!0)
        }), this.options.play.swap && d.css({
            display: "none"
        })),
        this.options.pagination.active && (a = $("<ul>", {
            "class": "slidesjs-pagination"
        }).appendTo(e), $.each(new Array(this.data.total),
        function(i) {
            var e, s;
            return e = $("<li>", {
                "class": "slidesjs-pagination-item"
            }).appendTo(a),
            s = $("<a>", {
                href: "#",
                "data-slidesjs-item": i,
                html: i + 1
            }).appendTo(e),
            setTimeout(function() {
                a.css({
                    left: 100 * ((t - a.outerWidth()) / 2 / t) + "%"
                })
            },
            100),
            s.click(function(t) {
                return t.preventDefault(),
                r.stop(!0),
                r.goto(1 * $(t.currentTarget).attr("data-slidesjs-item") + 1)
            })
        }), a.css("left", (e.innerWidth() - a.width()) / 2)),
        $(window).bind("resize",
        function() {
            return r.update()
        }),
        this._setActive(),
        this.options.play.auto && this.play(),
        this.options.callback.loaded(this.options.start)
    },
    t.prototype._setActive = function(t) {
        var i, e;
        return i = $(this.element),
        this.data = $.data(this),
        e = t > -1 ? t: this.data.current,
        $(".active", i).removeClass("active"),
        $(".slidesjs-pagination li:eq(" + e + ") a", i).addClass("active")
    },
    t.prototype.update = function() {
        var t, i, e;
        return t = $(this.element),
        this.data = $.data(this),
        $(".slidesjs-control", t).children(":not(:eq(" + this.data.current + "))").css({
            display: "none",
            left: 0,
            zIndex: 0
        }),
        e = t.width(),
        i = this.options.height,
        this.options.width = e,
        this.options.height = i,
        $(".slidesjs-control, .slidesjs-container", t).css({
            width: e,
            height: i
        })
    },
    t.prototype.next = function(t) {
        var i;
        return i = $(this.element),
        this.data = $.data(this),
        $.data(this, "direction", "next"),
        void 0 === t && (t = this.options.navigation.effect),
        "fade" === t ? this._fade() : this._slide()
    },
    t.prototype.previous = function(t) {
        var i;
        return i = $(this.element),
        this.data = $.data(this),
        $.data(this, "direction", "previous"),
        void 0 === t && (t = this.options.navigation.effect),
        "fade" === t ? this._fade() : this._slide()
    },
    t.prototype.goto = function(t) {
        var i, e;
        if (i = $(this.element), this.data = $.data(this), void 0 === e && (e = this.options.pagination.effect), t > this.data.total ? t = this.data.total: 1 > t && (t = 1), "number" == typeof t) return "fade" === e ? this._fade(t) : this._slide(t);
        if ("string" == typeof t) {
            if ("first" === t) return "fade" === e ? this._fade(0) : this._slide(0);
            if ("last" === t) return "fade" === e ? this._fade(this.data.total) : this._slide(this.data.total)
        }
    },
    t.prototype._setuptouch = function() {
        var t, i, e, s;
        return t = $(this.element),
        this.data = $.data(this),
        s = $(".slidesjs-control", t),
        i = this.data.current + 1,
        e = this.data.current - 1,
        0 > e && (e = this.data.total - 1),
        i > this.data.total - 1 && (i = 0),
        s.children(":eq(" + i + ")").css({
            display: "block",
            left: this.options.width
        }),
        s.children(":eq(" + e + ")").css({
            display: "block",
            left: -this.options.width
        })
    },
    t.prototype._touchstart = function(t) {
        var i, e;
        return i = $(this.element),
        this.data = $.data(this),
        e = t.originalEvent.touches[0],
        this._setuptouch(),
        $.data(this, "touchtimer", Number(new Date)),
        $.data(this, "touchstartx", e.pageX),
        $.data(this, "touchstarty", e.pageY),
        t.stopPropagation()
    },
    t.prototype._touchend = function(t) {
        var i, e, s, a, n, o, d, r = this;
        return i = $(this.element),
        this.data = $.data(this),
        o = t.originalEvent.touches[0],
        a = $(".slidesjs-control", i),
        a.position().left > .5 * this.options.width || a.position().left > .1 * this.options.width && Number(new Date) - this.data.touchtimer < 250 ? ($.data(this, "direction", "previous"), this._slide()) : a.position().left < -(.5 * this.options.width) || a.position().left < -(.1 * this.options.width) && Number(new Date) - this.data.touchtimer < 250 ? ($.data(this, "direction", "next"), this._slide()) : (s = this.data.vendorPrefix, d = s + "Transform", e = s + "TransitionDuration", n = s + "TransitionTimingFunction", a[0].style[d] = "translateX(0px)", a[0].style[e] = .85 * this.options.effect.slide.speed + "ms"),
        a.on("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd",
        function() {
            return s = r.data.vendorPrefix,
            d = s + "Transform",
            e = s + "TransitionDuration",
            n = s + "TransitionTimingFunction",
            a[0].style[d] = "",
            a[0].style[e] = "",
            a[0].style[n] = ""
        }),
        t.stopPropagation()
    },
    t.prototype._touchmove = function(t) {
        var i, e, s, a, n;
        return i = $(this.element),
        this.data = $.data(this),
        a = t.originalEvent.touches[0],
        e = this.data.vendorPrefix,
        s = $(".slidesjs-control", i),
        n = e + "Transform",
        $.data(this, "scrolling", Math.abs(a.pageX - this.data.touchstartx) < Math.abs(a.pageY - this.data.touchstarty)),
        this.data.animating || this.data.scrolling || (t.preventDefault(), this._setuptouch(), s[0].style[n] = "translateX(" + (a.pageX - this.data.touchstartx) + "px)"),
        t.stopPropagation()
    },
    t.prototype.play = function(t) {
        var i, e, s, a = this;
        return i = $(this.element),
        this.data = $.data(this),
        !this.data.playInterval && (t && (e = this.data.current, this.data.direction = "next", "fade" === this.options.play.effect ? this._fade() : this._slide()), $.data(this, "playInterval", setInterval(function() {
            return e = a.data.current,
            a.data.direction = "next",
            "fade" === a.options.play.effect ? a._fade() : a._slide()
        },
        this.options.play.interval)), s = $(".slidesjs-container", i), this.options.play.pauseOnHover && (s.unbind(), s.bind("mouseenter",
        function() {
            return a.stop()
        }), s.bind("mouseleave",
        function() {
            return a.options.play.restartDelay ? $.data(a, "restartDelay", setTimeout(function() {
                return a.play(!0)
            },
            a.options.play.restartDelay)) : a.play()
        })), $.data(this, "playing", !0), $(".slidesjs-play", i).addClass("slidesjs-playing"), this.options.play.swap) ? ($(".slidesjs-play", i).hide(), $(".slidesjs-stop", i).show()) : void 0
    },
    t.prototype.stop = function(t) {
        var i;
        return i = $(this.element),
        this.data = $.data(this),
        clearInterval(this.data.playInterval),
        this.options.play.pauseOnHover && t && $(".slidesjs-container", i).unbind(),
        $.data(this, "playInterval", null),
        $.data(this, "playing", !1),
        $(".slidesjs-play", i).removeClass("slidesjs-playing"),
        this.options.play.swap ? ($(".slidesjs-stop", i).hide(), $(".slidesjs-play", i).show()) : void 0
    },
    t.prototype._slide = function(t) {
        var i, e, s, a, n, o, d, r, l, h, c = this;
        return i = $(this.element),
        this.data = $.data(this),
        this.data.animating || t === this.data.current + 1 ? void 0 : ($.data(this, "animating", !0), e = this.data.current, t > -1 ? (t -= 1, h = t > e ? 1 : -1, s = t > e ? -this.options.width: this.options.width, n = t) : (h = "next" === this.data.direction ? 1 : -1, s = "next" === this.data.direction ? -this.options.width: this.options.width, n = e + h), -1 === n && (n = this.data.total - 1), n === this.data.total && (n = 0), this._setActive(n), d = $(".slidesjs-control", i), t > -1 && d.children(":not(:eq(" + e + "))").css({
            display: "none",
            left: 0,
            zIndex: 0
        }), d.children(":eq(" + n + ")").css({
            display: "block",
            left: h * this.options.width,
            zIndex: 10
        }), this.options.callback.start(e + 1), this.data.vendorPrefix ? (o = this.data.vendorPrefix, l = o + "Transform", a = o + "TransitionDuration", r = o + "TransitionTimingFunction", d[0].style[l] = "translateX(" + s + "px)", d[0].style[a] = this.options.effect.slide.speed + "ms", d.on("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd",
        function() {
            return d[0].style[l] = "",
            d[0].style[a] = "",
            d.children(":eq(" + n + ")").css({
                left: 0
            }),
            d.children(":eq(" + e + ")").css({
                display: "none",
                left: 0,
                zIndex: 0
            }),
            $.data(c, "current", n),
            $.data(c, "animating", !1),
            d.unbind("transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd"),
            d.children(":not(:eq(" + n + "))").css({
                display: "none",
                left: 0,
                zIndex: 0
            }),
            c.data.touch && c._setuptouch(),
            c.options.callback.complete(n + 1)
        })) : d.stop().animate({
            left: s
        },
        this.options.effect.slide.speed,
        function() {
            return d.css({
                left: 0
            }),
            d.children(":eq(" + n + ")").css({
                left: 0
            }),
            d.children(":eq(" + e + ")").css({
                display: "none",
                left: 0,
                zIndex: 0
            },
            $.data(c, "current", n), $.data(c, "animating", !1), c.options.callback.complete(n + 1))
        }))
    },
    t.prototype._fade = function(t) {
        var i, e, s, a, n, o = this;
        return i = $(this.element),
        this.data = $.data(this),
        this.data.animating || t === this.data.current + 1 ? void 0 : ($.data(this, "animating", !0), e = this.data.current, t ? (t -= 1, n = t > e ? 1 : -1, s = t) : (n = "next" === this.data.direction ? 1 : -1, s = e + n), -1 === s && (s = this.data.total - 1), s === this.data.total && (s = 0), this._setActive(s), a = $(".slidesjs-control", i), a.children(":eq(" + s + ")").css({
            display: "none",
            left: 0,
            zIndex: 10
        }), this.options.callback.start(e + 1), this.options.effect.fade.crossfade ? (a.children(":eq(" + this.data.current + ")").stop().fadeOut(this.options.effect.fade.speed), a.children(":eq(" + s + ")").stop().fadeIn(this.options.effect.fade.speed,
        function() {
            return a.children(":eq(" + s + ")").css({
                zIndex: 0
            }),
            $.data(o, "animating", !1),
            $.data(o, "current", s),
            o.options.callback.complete(s + 1)
        })) : a.children(":eq(" + e + ")").stop().fadeOut(this.options.effect.fade.speed,
        function() {
            return a.children(":eq(" + s + ")").stop().fadeIn(o.options.effect.fade.speed,
            function() {
                return a.children(":eq(" + s + ")").css({
                    zIndex: 10
                })
            }),
            $.data(o, "animating", !1),
            $.data(o, "current", s),
            o.options.callback.complete(s + 1)
        }))
    },
    t.prototype._getVendorPrefix = function() {
        var t, i, e, s, a;
        for (t = document.body || document.documentElement, e = t.style, s = "transition", a = ["Moz", "Webkit", "Khtml", "O", "ms"], s = s.charAt(0).toUpperCase() + s.substr(1), i = 0; i < a.length;) {
            if ("string" == typeof e[a[i] + s]) return a[i];
            i++
        }
        return ! 1
    },
    function(i, e, s) {
        var a = i && i.jquery ? i: e;
        a.is(":visible") ? new t(i, e, s) : a.on("resize",
        function() {
            new t(i, e, s)
        })
    }
});