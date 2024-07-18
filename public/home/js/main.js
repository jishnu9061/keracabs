!(function (e) {
  "use strict";
  function t(t) {
    e(t).length > 0 &&
      e(t).each(function () {
        var t = e(this).find("a");
        e(this)
          .find(t)
          .each(function () {
            e(this).on("click", function () {
              var t = e(this.getAttribute("href"));
              t.length &&
                (event.preventDefault(),
                e("html, body")
                  .stop()
                  .animate({ scrollTop: t.offset().top - 10 }, 1e3));
            });
          });
      });
  }
  if (
    (e(window).on("load", function () {
      e(".preloader").fadeOut();
    }),
    e(".nice-select").length && e(".nice-select").niceSelect(),
    e(".preloader").length > 0 &&
      e(".preloaderCls").each(function () {
        e(this).on("click", function (t) {
          t.preventDefault(), e(".preloader").css("display", "none");
        });
      }),
    (e.fn.vsmobilemenu = function (t) {
      var a = e.extend(
        {
          menuToggleBtn: ".th-menu-toggle ",
          bodyToggleClass: "th-body-visible",
          subMenuClass: "th-submenu",
          subMenuParent: "th-item-has-children",
          subMenuParentToggle: "th-active",
          meanExpandClass: "th-mean-expand",
          appendElement: '<span class="th-mean-expand"></span>',
          subMenuToggleClass: "th-open",
          toggleSpeed: 400,
        },
        t
      );
      return this.each(function () {
        var t = e(this);
        function s() {
          t.toggleClass(a.bodyToggleClass);
          var s = "." + a.subMenuClass;
          e(s).each(function () {
            e(this).hasClass(a.subMenuToggleClass) &&
              (e(this).removeClass(a.subMenuToggleClass),
              e(this).css("display", "none"),
              e(this).parent().removeClass(a.subMenuParentToggle));
          });
        }
        t.find("li").each(function () {
          var t = e(this).find("ul");
          t.addClass(a.subMenuClass),
            t.css("display", "none"),
            t.parent().addClass(a.subMenuParent),
            t.prev("a").append(a.appendElement),
            t.next("a").append(a.appendElement);
        });
        var n = "." + a.meanExpandClass;
        e(n).each(function () {
          e(this).on("click", function (t) {
            var s;
            t.preventDefault(),
              (s = e(this).parent()),
              e(s).next("ul").length > 0
                ? (e(s).parent().toggleClass(a.subMenuParentToggle),
                  e(s).next("ul").slideToggle(a.toggleSpeed),
                  e(s).next("ul").toggleClass(a.subMenuToggleClass))
                : e(s).prev("ul").length > 0 &&
                  (e(s).parent().toggleClass(a.subMenuParentToggle),
                  e(s).prev("ul").slideToggle(a.toggleSpeed),
                  e(s).prev("ul").toggleClass(a.subMenuToggleClass));
          });
        }),
          e(a.menuToggleBtn).each(function () {
            e(this).on("click", function () {
              s();
            });
          }),
          t.on("click", function (e) {
            e.stopPropagation(), s();
          }),
          t.find("div").on("click", function (e) {
            e.stopPropagation();
          });
      });
    }),
    e(".th-menu-wrapper").vsmobilemenu(),
    e(window).on("scroll", function () {
      var t = e(this).scrollTop();
      function a() {
        t > 400 &&
          (e(".sticky-wrapper").addClass("sticky"),
          e(".sticky-wrapper").removeClass("will-sticky"));
      }
      a(),
        t > 150
          ? (e(".sticky-wrapper").addClass("will-sticky"), a())
          : (e(".sticky-wrapper").removeClass("sticky"),
            e(".sticky-wrapper").removeClass("will-sticky"));
    }),
    t(".onepage-nav"),
    t(".scroll-down"),
    e(".scroll-top").length > 0)
  ) {
    var a = document.querySelector(".scroll-top"),
      s = document.querySelector(".scroll-top path"),
      n = s.getTotalLength();
    (s.style.transition = s.style.WebkitTransition = "none"),
      (s.style.strokeDasharray = n + " " + n),
      (s.style.strokeDashoffset = n),
      s.getBoundingClientRect(),
      (s.style.transition = s.style.WebkitTransition =
        "stroke-dashoffset 10ms linear");
    var o = function () {
      var t = e(window).scrollTop(),
        a = e(document).height() - e(window).height(),
        o = n - (t * n) / a;
      s.style.strokeDashoffset = o;
    };
    o(), e(window).scroll(o);
    jQuery(window).on("scroll", function () {
      jQuery(this).scrollTop() > 50
        ? jQuery(a).addClass("show")
        : jQuery(a).removeClass("show");
    }),
      jQuery(a).on("click", function (e) {
        return (
          e.preventDefault(),
          jQuery("html, body").animate({ scrollTop: 0 }, 750),
          !1
        );
      });
  }
  e("[data-bg-src]").length > 0 &&
    e("[data-bg-src]").each(function () {
      var t = e(this).attr("data-bg-src");
      e(this).css("background-image", "url(" + t + ")"),
        e(this).removeAttr("data-bg-src").addClass("background-image");
    }),
    e("[data-bg-src]").length > 0 &&
      e("[data-bg-src]").each(function () {
        var t = e(this).attr("data-bg-src");
        e(this).css("background-image", "url(" + t + ")"),
          e(this).removeAttr("data-bg-src").addClass("background-image");
      }),
    e("[data-mask-src]").length > 0 &&
      e("[data-mask-src]").each(function () {
        var t = e(this).attr("data-mask-src");
        e(this).css({
          "mask-image": "url(" + t + ")",
          "-webkit-mask-image": "url(" + t + ")",
        }),
          e(this).removeAttr("data-mask-src");
      }),
    e(".th-carousel").each(function () {
      var t = e(this);
      function a(e) {
        return t.data(e);
      }
      var s =
          '<button type="button" class="slick-prev"><i class="' +
          a("prev-arrow") +
          '"></i></button>',
        n =
          '<button type="button" class="slick-next"><i class="' +
          a("next-arrow") +
          '"></i></button>';
      e("[data-slick-next]").each(function () {
        e(this).on("click", function (t) {
          t.preventDefault(), e(e(this).data("slick-next")).slick("slickNext");
        });
      }),
        e("[data-slick-prev]").each(function () {
          e(this).on("click", function (t) {
            t.preventDefault(),
              e(e(this).data("slick-prev")).slick("slickPrev");
          });
        }),
        1 == a("arrows") &&
          (t.closest(".arrow-wrap").length ||
            t.closest(".container").parent().addClass("arrow-wrap")),
        t.slick({
          dots: !!a("dots"),
          fade: !!a("fade"),
          arrows: !!a("arrows"),
          speed: a("speed") ? a("speed") : 1e3,
          asNavFor: !!a("asnavfor") && a("asnavfor"),
          autoplay: 0 != a("autoplay"),
          infinite: 0 != a("infinite"),
          slidesToShow: a("slide-show") ? a("slide-show") : 1,
          adaptiveHeight: !!a("adaptive-height"),
          centerMode: !!a("center-mode"),
          autoplaySpeed: a("autoplay-speed") ? a("autoplay-speed") : 8e3,
          centerPadding: a("center-padding") ? a("center-padding") : "0",
          focusOnSelect: 0 != a("focuson-select"),
          pauseOnFocus: !!a("pauseon-focus"),
          pauseOnHover: !!a("pauseon-hover"),
          variableWidth: !!a("variable-width"),
          vertical: !!a("vertical"),
          verticalSwiping: !!a("vertical"),
          prevArrow: a("prev-arrow")
            ? s
            : '<button type="button" class="slick-prev"><i class="far fa-arrow-left"></i></button>',
          nextArrow: a("next-arrow")
            ? n
            : '<button type="button" class="slick-next"><i class="far fa-arrow-right"></i></button>',
          rtl: "rtl" == e("html").attr("dir"),
          responsive: [
            {
              breakpoint: 1600,
              settings: {
                arrows: !!a("xl-arrows"),
                dots: !!a("xl-dots"),
                slidesToShow: a("xl-slide-show")
                  ? a("xl-slide-show")
                  : a("slide-show"),
                centerMode: !!a("xl-center-mode"),
                centerPadding: 0,
              },
            },
            {
              breakpoint: 1400,
              settings: {
                arrows: !!a("ml-arrows"),
                dots: !!a("ml-dots"),
                slidesToShow: a("ml-slide-show")
                  ? a("ml-slide-show")
                  : a("slide-show"),
                centerMode: !!a("ml-center-mode"),
                centerPadding: 0,
              },
            },
            {
              breakpoint: 1200,
              settings: {
                arrows: !!a("lg-arrows"),
                dots: !!a("lg-dots"),
                slidesToShow: a("lg-slide-show")
                  ? a("lg-slide-show")
                  : a("slide-show"),
                centerMode: !!a("lg-center-mode") && a("lg-center-mode"),
                centerPadding: 0,
              },
            },
            {
              breakpoint: 992,
              settings: {
                arrows: !!a("md-arrows"),
                dots: !!a("md-dots"),
                slidesToShow: a("md-slide-show") ? a("md-slide-show") : 1,
                centerMode: !!a("md-center-mode") && a("md-center-mode"),
                centerPadding: 0,
              },
            },
            {
              breakpoint: 768,
              settings: {
                arrows: !!a("sm-arrows"),
                dots: !!a("sm-dots"),
                slidesToShow: a("sm-slide-show") ? a("sm-slide-show") : 1,
                centerMode: !!a("sm-center-mode") && a("sm-center-mode"),
                centerPadding: 0,
              },
            },
            {
              breakpoint: 576,
              settings: {
                arrows: !!a("xs-arrows"),
                dots: !!a("xs-dots"),
                slidesToShow: a("xs-slide-show") ? a("xs-slide-show") : 1,
                centerMode: !!a("xs-center-mode") && a("xs-center-mode"),
                centerPadding: 0,
              },
            },
          ],
        });
    }),
    e("[data-ani-duration]").each(function () {
      var t = e(this).data("ani-duration");
      e(this).css("animation-duration", t);
    }),
    e("[data-ani-delay]").each(function () {
      var t = e(this).data("ani-delay");
      e(this).css("animation-delay", t);
    }),
    e("[data-ani]").each(function () {
      var t = e(this).data("ani");
      e(this).addClass(t),
        e(".slick-current [data-ani]").addClass("th-animated");
    }),
    e(".th-carousel").on("afterChange", function (t, a, s, n) {
      e(a.$slides).find("[data-ani]").removeClass("th-animated"),
        e(a.$slides[s]).find("[data-ani]").addClass("th-animated");
    });
  var i,
    r,
    l,
    c = ".ajax-contact",
    d = "is-invalid",
    u = '[name="email"]',
    p = '[name="name"],[name="email"],[name="subject"],[name="message"]',
    h = e(".form-messages");
  function f() {
    var t = e(c).serialize();
    m() &&
      jQuery
        .ajax({ url: e(c).attr("action"), data: t, type: "POST" })
        .done(function (t) {
          h.removeClass("error"),
            h.addClass("success"),
            h.text(t),
            e(c + ' input:not([type="submit"]),' + c + " textarea").val("");
        })
        .fail(function (e) {
          h.removeClass("success"),
            h.addClass("error"),
            "" !== e.responseText
              ? h.html(e.responseText)
              : h.html(
                  "Oops! An error occured and your message could not be sent."
                );
        });
  }
  function m() {
    var t,
      a = !0;
    return (
      (function (s) {
        s = s.split(",");
        for (var n = 0; n < s.length; n++)
          (t = c + " " + s[n]),
            e(t).val()
              ? (e(t).removeClass(d), (a = !0))
              : (e(t).addClass(d), (a = !1));
      })(p),
      e(u).val() &&
      e(u)
        .val()
        .match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)
        ? (e(u).removeClass(d), (a = !0))
        : (e(u).addClass(d), (a = !1)),
      a
    );
  }
  if (
    (e(c).on("submit", function (e) {
      e.preventDefault(), f();
    }),
    e(".ajax-booking").length > 0)
  ) {
    (c = ".ajax-booking"),
      (u = '[name="email"]'),
      (d = "is-invalid"),
      (p =
        '[name="name"],[name="number"],[name="email"],[name="s-destination"],[name="e-destination"],[name="passenger"],[name="date"],[name="time"],[name="vehicle"],[name="message"]'),
      (h = e(".form-messages"));
    function f() {
      var t = e(c).serialize();
      m() &&
        jQuery
          .ajax({ url: e(c).attr("action"), data: t, type: "POST" })
          .done(function (t) {
            h.removeClass("error"),
              h.addClass("success"),
              h.text(t),
              e(c + ' input:not([type="submit"]),' + c + " textarea").val("");
          })
          .fail(function (e) {
            h.removeClass("success"),
              h.addClass("error"),
              "" !== e.responseText
                ? h.html(e.responseText)
                : h.html(
                    "Oops! An error occured and your message could not be sent."
                  );
          });
    }
    function m() {
      var t,
        a = !0;
      return (
        (function (s) {
          s = s.split(",");
          for (var n = 0; n < s.length; n++)
            (t = c + " " + s[n]),
              e(t).val()
                ? (e(t).removeClass(d), (a = !0))
                : (e(t).addClass(d), (a = !1));
        })(p),
        e(u).val() &&
        e(u)
          .val()
          .match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/)
          ? (e(u).removeClass(d), (a = !0))
          : (e(u).addClass(d), (a = !1)),
        a
      );
    }
    e(c).on("submit", function (e) {
      e.preventDefault(), f();
    });
  }
  function g(t, a, s, n) {
    e(a).on("click", function (a) {
      a.preventDefault(), e(t).addClass(n);
    }),
      e(t).on("click", function (a) {
        a.stopPropagation(), e(t).removeClass(n);
      }),
      e(t + " > div").on("click", function (a) {
        a.stopPropagation(), e(t).addClass(n);
      }),
      e(s).on("click", function (a) {
        a.preventDefault(), a.stopPropagation(), e(t).removeClass(n);
      });
  }
  function g(t, a, s, n) {
    e(a).on("click", function (a) {
      a.preventDefault(), e(t).addClass(n);
    }),
      e(t).on("click", function (a) {
        a.stopPropagation(), e(t).removeClass(n);
      }),
      e(t + " > div").on("click", function (a) {
        a.stopPropagation(), e(t).addClass(n);
      }),
      e(s).on("click", function (a) {
        a.preventDefault(), a.stopPropagation(), e(t).removeClass(n);
      });
  }
  function v(e) {
    return parseInt(e, 10);
  }
  (i = ".popup-search-box"),
    (r = ".searchClose"),
    (l = "show"),
    e(".searchBoxToggler").on("click", function (t) {
      t.preventDefault(), e(i).addClass(l);
    }),
    e(i).on("click", function (t) {
      t.stopPropagation(), e(i).removeClass(l);
    }),
    e(i)
      .find("form")
      .on("click", function (t) {
        t.stopPropagation(), e(i).addClass(l);
      }),
    e(r).on("click", function (t) {
      t.preventDefault(), t.stopPropagation(), e(i).removeClass(l);
    }),
    g(".sidemenu-wrapper", ".sideMenuToggler", ".sideMenuCls", "show"),
    g(".shopping-cart", ".sideMenuToggler2", ".sideMenuCls", "show"),
    e(".popup-image").magnificPopup({
      type: "image",
      gallery: { enabled: !0 },
    }),
    e(".popup-video").magnificPopup({ type: "iframe" }),
    e(".popup-content").magnificPopup({ type: "inline", midClick: !0 }),
    e(".portfolio-filter-active").imagesLoaded(function () {
      var t = ".portfolio-filter-active";
      if (e(t).length > 0) {
        var a = e(t).isotope({
          itemSelector: ".filter-item",
          filter: "*",
          masonry: { columnWidth: 1 },
        });
        e(".filter-menu-active").on("click", "button", function () {
          var t = e(this).attr("data-filter");
          a.isotope({ filter: t });
        }),
          e(".filter-menu-active").on("click", "button", function (t) {
            t.preventDefault(),
              e(this).addClass("active"),
              e(this).siblings(".active").removeClass("active");
          });
      }
    }),
    e(".masonary-active").imagesLoaded(function () {
      e(".masonary-active").length > 0 &&
        e(".masonary-active").isotope({
          itemSelector: ".filter-item",
          filter: "*",
          masonry: { columnWidth: 1 },
        });
    }),
    e(".filter-active-cat1").imagesLoaded(function () {
      if (e(".filter-active-cat1").length > 0) {
        var t = e(".filter-active-cat1").isotope({
          itemSelector: ".filter-item",
          filter: ".cat1",
          masonry: { columnWidth: 1 },
        });
        e(".filter-menu-active").on("click", "button", function () {
          var a = e(this).attr("data-filter");
          t.isotope({ filter: a });
        }),
          e(".filter-menu-active").on("click", "button", function (t) {
            t.preventDefault(),
              e(this).addClass("active"),
              e(this).siblings(".active").removeClass("active");
          });
      }
    }),
    e(".date-pick").datetimepicker({
      timepicker: !1,
      datepicker: !0,
      format: "d-m-y",
      step: 10,
    }),
    e(".time-pick").datetimepicker({ datepicker: !1, format: "H:i", step: 30 }),
    new WOW().init(),
    e(".counter-number").counterUp({ delay: 10, time: 1e3 }),
    (e.fn.vsTab = function (t) {
      var a = e.extend({ sliderTab: !1, tabButton: "button" }, t);
      e(this).each(function () {
        var t = e(this),
          s = t.find(a.tabButton);
        t.append('<span class="indicator"></span>');
        var n = t.find(".indicator");
        if (
          (s.on("click", function (t) {
            t.preventDefault();
            var s = e(this);
            s.addClass("active").siblings().removeClass("active"),
              a.sliderTab
                ? e(o).slick("slickGoTo", s.data("slide-go-to"))
                : r();
          }),
          a.sliderTab)
        ) {
          var o = t.data("asnavfor"),
            i = 0;
          s.each(function () {
            var s = e(this);
            s.attr("data-slide-go-to", i),
              i++,
              s.hasClass("active") &&
                e(o).slick("slickGoTo", s.data("slide-go-to")),
              e(o).on("beforeChange", function (e, s, n, o) {
                t
                  .find(a.tabButton + '[data-slide-go-to="' + o + '"]')
                  .addClass("active")
                  .siblings()
                  .removeClass("active"),
                  r();
              });
          });
        }
        function r() {
          var o = t.find(a.tabButton + ".active"),
            i = o.css("height"),
            r = o.css("width"),
            l = o.position().top + "px",
            c = o.position().left + "px";
          n.get(0).style.setProperty("--height-set", i),
            n.get(0).style.setProperty("--width-set", r),
            n.get(0).style.setProperty("--pos-y", l),
            n.get(0).style.setProperty("--pos-x", c),
            e(s).first().position().left == o.position().left
              ? n.addClass("start").removeClass("center").removeClass("end")
              : e(s).last().position().left == o.position().left
              ? n.addClass("end").removeClass("center").removeClass("start")
              : n.addClass("center").removeClass("start").removeClass("end");
        }
        r();
      });
    }),
    e(".taxi-tab").length &&
      e(".taxi-tab").vsTab({ sliderTab: !0, tabButton: ".th-btn" }),
    e(".progress-bar").waypoint(
      function () {
        e(".progress-bar").css({
          animation: "animate-positive 1.8s",
          opacity: "1",
        });
      },
      { offset: "75%" }
    ),
    (e.fn.sectionPosition = function (t, a) {
      e(this).each(function () {
        var s,
          n,
          o,
          i,
          r,
          l = e(this);
        (s = Math.floor(l.height() / 2)),
          (n = l.attr(t)),
          (o = l.attr(a)),
          (i = v(e(o).css("padding-top"))),
          (r = v(e(o).css("padding-bottom"))),
          "top-half" === n
            ? (e(o).css("padding-bottom", r + s + "px"),
              l.css("margin-top", "-" + s + "px"))
            : "bottom-half" === n &&
              (e(o).css("padding-top", i + s + "px"),
              l.css("margin-bottom", "-" + s + "px"));
      });
    });
  function b() {
    e(".feature-circle .progressbar").each(function () {
      var t = e(this).attr("data-path-color"),
        a = e(this).offset().top,
        s = e(window).scrollTop(),
        n = e(this).find(".circle").attr("data-percent"),
        o = (parseInt(n, 10), parseInt(100, 10), e(this).data("animate"));
      a < s + e(window).height() - 30 &&
        !o &&
        (e(this).data("animate", !0),
        e(this)
          .find(".circle")
          .circleProgress({
            startAngle: -Math.PI / 2,
            value: n / 100,
            size: 138,
            thickness: 12,
            emptyFill: "#e30d161a",
            lineCap: "round",
            fill: { color: t },
          })
          .on("circle-animation-progress", function (t, a, s) {
            e(this)
              .find(".circle-num")
              .text((100 * s).toFixed(0) + "%");
          })
          .stop());
    }),
      e(".skill-circle .progressbar").each(function () {
        var t = e(this).offset().top,
          a = e(window).scrollTop(),
          s = e(this).find(".circle").attr("data-percent"),
          n = (parseInt(s, 10), parseInt(100, 10), e(this).data("animate"));
        t < a + e(window).height() - 30 &&
          !n &&
          (e(this).data("animate", !0),
          e(this)
            .find(".circle")
            .circleProgress({
              startAngle: -Math.PI / 2,
              value: s / 100,
              size: 100,
              thickness: 8,
              emptyFill: "#E0E0E0",
              lineCap: "round",
              fill: { gradient: ["#F11F22", "#F2891D"] },
            })
            .on("circle-animation-progress", function (t, a, s) {
              e(this)
                .find(".circle-num")
                .text((100 * s).toFixed(0) + "%");
            })
            .stop());
      });
  }
  e("[data-sec-pos]").length &&
    e("[data-sec-pos]").imagesLoaded(function () {
      e("[data-sec-pos]").sectionPosition("data-sec-pos", "data-pos-for");
    }),
    b(),
    e(window).scroll(b),
    (e.fn.shapeMockup = function () {
      e(this).each(function () {
        var t = e(this),
          a = t.data("top"),
          s = t.data("right"),
          n = t.data("bottom"),
          o = t.data("left");
        t.css({ top: a, right: s, bottom: n, left: o })
          .removeAttr("data-top")
          .removeAttr("data-right")
          .removeAttr("data-bottom")
          .removeAttr("data-left")
          .parent()
          .addClass("shape-mockup-wrap");
      });
    }),
    e(".shape-mockup") && e(".shape-mockup").shapeMockup(),
    e('[data-bs-toggle="tab"]').on("shown.bs.tab", function (t) {
      e(".th-carousel").slick("setPosition");
    }),
    e("#ship-to-different-address-checkbox").on("change", function () {
      e(this).is(":checked")
        ? e("#ship-to-different-address").next(".shipping_address").slideDown()
        : e("#ship-to-different-address").next(".shipping_address").slideUp();
    }),
    e(".woocommerce-form-login-toggle a").on("click", function (t) {
      t.preventDefault(), e(".woocommerce-form-login").slideToggle();
    }),
    e(".woocommerce-form-coupon-toggle a").on("click", function (t) {
      t.preventDefault(), e(".woocommerce-form-coupon").slideToggle();
    }),
    e(".shipping-calculator-button").on("click", function (t) {
      t.preventDefault(),
        e(this).next(".shipping-calculator-form").slideToggle();
    }),
    e('.wc_payment_methods input[type="radio"]:checked')
      .siblings(".payment_box")
      .show(),
    e('.wc_payment_methods input[type="radio"]').each(function () {
      e(this).on("change", function () {
        e(".payment_box").slideUp(),
          e(this).siblings(".payment_box").slideDown();
      });
    }),
    e(".rating-select .stars a").each(function () {
      e(this).on("click", function (t) {
        t.preventDefault(),
          e(this).siblings().removeClass("active"),
          e(this).parent().parent().addClass("selected"),
          e(this).addClass("active");
      });
    }),
    e(".quantity-plus").each(function () {
      e(this).on("click", function (t) {
        t.preventDefault();
        var a = e(this).siblings(".qty-input"),
          s = parseInt(a.val(), 10);
        isNaN(s) || a.val(s + 1);
      });
    }),
    e(".quantity-minus").each(function () {
      e(this).on("click", function (t) {
        t.preventDefault();
        var a = e(this).siblings(".qty-input"),
          s = parseInt(a.val(), 10);
        !isNaN(s) && s > 1 && a.val(s - 1);
      });
    }),
    e(document).ready(function () {
      e(".revealator-delay1").addClass("no-transform");
    });
})(jQuery);
