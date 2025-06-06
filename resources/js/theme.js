// Select all theme toggle buttons
const themeToggleButtons = document.querySelectorAll(".theme-toggle");

// Initialize icons based on current theme
function initializeIcons() {
    const isDarkMode = localStorage.getItem("color-theme") === "dark" ||
        (!("color-theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches);

    themeToggleButtons.forEach(button => {
        const darkIcon = button.querySelector("[id$='-dark-icon']");
        const lightIcon = button.querySelector("[id$='-light-icon']");

        if (isDarkMode) {
            lightIcon.classList.remove("hidden");
            darkIcon.classList.add("hidden");
        } else {
            darkIcon.classList.remove("hidden");
            lightIcon.classList.add("hidden");
        }
    });
}

// Toggle theme and update icons
function toggleTheme() {
    const isDarkMode = document.documentElement.classList.contains("dark");

    themeToggleButtons.forEach(button => {
        const darkIcon = button.querySelector("[id$='-dark-icon']");
        const lightIcon = button.querySelector("[id$='-light-icon']");

        // Toggle icons
        darkIcon.classList.toggle("hidden");
        lightIcon.classList.toggle("hidden");
    });

    // Toggle theme
    if (isDarkMode) {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("color-theme", "light");
    } else {
        document.documentElement.classList.add("dark");
        localStorage.setItem("color-theme", "dark");
    }
}

// Initialize icons on page load
initializeIcons();

// Add click event listeners to all theme toggle buttons
themeToggleButtons.forEach(button => {
    button.addEventListener("click", toggleTheme);
});
