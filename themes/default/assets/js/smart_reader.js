/**
 * Smart Reader - Read-to-Earn logic with Anti-Cheat
 */
class SmartReader {
  constructor(articleId, targetTime = 60) {
    this.articleId = articleId;
    this.targetTime = targetTime;
    this.timeSpent = 0;
    this.isFocused = true;
    this.hasRewarded = false;
    this.maxScroll = 0;

    this.init();
  }

  init() {
    // Focus tracking
    window.onfocus = () => (this.isFocused = true);
    window.onblur = () => (this.isFocused = false);

    // Scroll tracking
    document.addEventListener("scroll", () => {
      const scrollPercent =
        ((window.scrollY + window.innerHeight) /
          document.documentElement.scrollHeight) *
        100;
      if (scrollPercent > this.maxScroll) this.maxScroll = scrollPercent;
    });

    // Timer
    this.timer = setInterval(() => {
      if (this.isFocused && !this.hasRewarded) {
        this.timeSpent++;
        this.updateUI();

        if (this.timeSpent >= this.targetTime && this.maxScroll > 70) {
          this.completeRead();
        }
      }
    }, 1000);
  }

  updateUI() {
    const progress = Math.min((this.timeSpent / this.targetTime) * 100, 100);
    const bar = document.getElementById("reader-progress-bar");
    if (bar) bar.style.width = progress + "%";

    if (progress >= 100) {
      const label = document.getElementById("reader-status-label");
      if (label) label.innerText = "Reward Ready! Scroll to bottom.";
    }
  }

  completeRead() {
    this.hasRewarded = true;
    clearInterval(this.timer);

    fetch(window.appConfig.baseUrl + "/api/quest/news-read", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        article_id: this.articleId,
        time_spent: this.timeSpent,
        scroll_depth: this.maxScroll,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Congratulations! Earning 20 G-Coins for reading.");
          if (typeof refreshResourceHUD === "function") refreshResourceHUD();
        }
      });
  }
}
