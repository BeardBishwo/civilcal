// 3D tilt effect for homepage category cards
(function(){
  function initTilt(){
    // Ensure homepage body class exists for CSS targeting (non-invasive fallback)
    try {
      if (document.body && !document.body.classList.contains('index-page') && document.querySelector('.calculator-grid')) {
        document.body.classList.add('index-page');
      }
    } catch (e) {}

    var cards = document.querySelectorAll('.category-card');
    if (!cards || cards.length === 0) return;
    cards.forEach(function(card){
      card.addEventListener('mousemove', function(e){
        var rect = card.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        var centerX = rect.width / 2;
        var centerY = rect.height / 2;
        var rotateY = (x - centerX) / 25;
        var rotateX = (centerY - y) / 25;
        card.style.transform = 'perspective(1000px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-10px)';
      });
      card.addEventListener('mouseleave', function(){
        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(-10px)';
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTilt);
  } else {
    initTilt();
  }
})();
