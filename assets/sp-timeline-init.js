(function($){
    function initTimeline(root){
      if(!root || !window.gsap || !window.ScrollTrigger) return;
  
      var $root = $(root);
      var tlWrap = $root.find('.sp-timeline');
  
      gsap.registerPlugin(ScrollTrigger);
  
      gsap.to($root.find('.draw-line')[0], {
        scaleY: 1,
        ease: "none",
        scrollTrigger: {
          trigger: tlWrap[0],
          start: "top center",
          endTrigger: $root.find('.sp-timeline ul li:last-child')[0],
          end: "bottom center",
          scrub: true
        }
      });
  
      $root.find(".sp-timeline ul li").each(function (index, el) {
        var circle  = $(el).find(".circle")[0];
        var content = $(el).find(".content")[0];
        var year    = $(el).find(".year")[0];
  
        gsap.to(circle, {
          scrollTrigger: {
            trigger: el,
            start: "top center",
            end: "bottom center",
            toggleActions: "play none none reverse"
          },
          scale: 3,
          backgroundColor: getComputedStyle($root[0]).getPropertyValue('--sp-circle').trim() || "#006CFF",
          duration: 0.3,
          ease: "power1.out"
        });
  
        var tline = gsap.timeline({
          scrollTrigger: {
            trigger: el,
            start: "top center",
            end: "center center",
          toggleActions: "play none none reverse"
          }
        });
  
        tline.fromTo(
          content,
          { opacity: 0, y: 25 },
          { opacity: 1, y: 0, duration: 0.45, ease: "power1.out" },
          0
        ).fromTo(
          year,
          { opacity: 0, y: 8 },
          { opacity: 1, y: 0, duration: 0.25, ease: "power1.out" },
          0.05
        );
      });
    }
  
    var onInit = function(){
      if(!window.SP_TIMELINES) return;
      window.SP_TIMELINES.forEach(function(cfg){
        initTimeline(cfg.selector);
      });
      window.SP_TIMELINES = [];
    };
  
    $(document).ready(onInit);
  
    if (window.elementorFrontend) {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/sp_timeline.default', onInit);
    }
  })(jQuery);
  