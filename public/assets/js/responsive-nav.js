document.addEventListener("DOMContentLoaded", () => {
  const mainNavList = document.getElementById("mainNavList");
  const moreToolsItem = document.getElementById("moreToolsItem");
  const moreToolsMenu = document.getElementById("moreToolsMenu");

  // Safety check: if elements don't exist, we can't do anything
  if (!mainNavList || !moreToolsItem || !moreToolsMenu) return;

  let isResizing = false;

  function adaptMenu() {
    if (isResizing) return;
    isResizing = true;

    // Use requestAnimationFrame for performance during resize
    window.requestAnimationFrame(() => {
      // 1. COLLAPSE: Move items to dropdown if overflowing
      // We loop as long as the content is wider than the container
      // We use a safety counter to prevent infinite loops in edge cases
      let safety = 0;
      while (
        mainNavList.scrollWidth > mainNavList.clientWidth &&
        safety < 100
      ) {
        safety++;

        // Find all items that are NOT the "More Tools" button
        const candidates = Array.from(mainNavList.children).filter(
          (item) => item !== moreToolsItem
        );

        if (candidates.length === 0) break; // Nothing left to move

        // We move the LAST eligible item (the one right before More Tools)
        const itemToMove = candidates[candidates.length - 1];

        // Add a marker class so we know this item was moved by us (and not a static dropdown item)
        const link = itemToMove.querySelector("a");
        if (link) {
          link.classList.add("moved-item");
          // Optional: Remove grid-item if it exists to match layout, or keep it.
          // For now, we trust the CSS to handle .dropdown a vs .main-nav a
        }

        // Prepend to the dropdown menu (so it appears at the top)
        if (moreToolsMenu.firstChild) {
          moreToolsMenu.insertBefore(itemToMove, moreToolsMenu.firstChild);
        } else {
          moreToolsMenu.appendChild(itemToMove);
        }
      }

      // 2. EXPAND: Restore items if there is room
      // We verify if moving the top item back would fit.
      safety = 0;
      while (moreToolsMenu.children.length > 0 && safety < 100) {
        safety++;

        const itemToRestore = moreToolsMenu.firstElementChild;
        const link = itemToRestore.querySelector("a");

        // ONLY restore items that we moved there (marked with 'moved-item')
        // This prevents us from moving the static "Site Development", "Structural", etc items out.
        if (!link || !link.classList.remove("moved-item")) {
          // Attempt to remove class, returns true if existed (in slightly diff way)
          // Actually logic: check class first
          if (!link.classList.contains("moved-item")) break;
          link.classList.remove("moved-item");
        } else {
          // If it didn't have the class, break.
          // Wait, the if above is convoluted.
          // Redo:
        }

        // Check class cleanly
        if (!link.classList.contains("moved-item")) {
          break;
        }

        // Optimistically move it back
        mainNavList.insertBefore(itemToRestore, moreToolsItem);

        // Check if we caused an overflow
        if (mainNavList.scrollWidth > mainNavList.clientWidth) {
          // Oops, it doesn't fit. Put it back in the dropdown and stop trying.
          link.classList.add("moved-item"); // Re-add the marker
          if (moreToolsMenu.firstChild) {
            moreToolsMenu.insertBefore(itemToRestore, moreToolsMenu.firstChild);
          } else {
            moreToolsMenu.appendChild(itemToRestore);
          }
          break;
        }
      }

      // Final check: Toggle visibility of "More Tools" if it's empty?
      // Since we have static items, it's never empty. So we don't hide it.

      isResizing = false;
    });
  }

  // Initial check on load
  adaptMenu();

  // Check on resize
  window.addEventListener("resize", adaptMenu);
});
