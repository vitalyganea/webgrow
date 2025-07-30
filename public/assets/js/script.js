'use strict';



/**
 * addEvent on element
 */

const addEventOnElem = function (elem, type, callback) {
  if (elem.length > 1) {
    for (let i = 0; i < elem.length; i++) {
      elem[i].addEventListener(type, callback);
    }
  } else {
    elem.addEventListener(type, callback);
  }
}



/**
 * navbar toggle
 */

const navbar = document.querySelector("[data-navbar]");
const navbarLinks = document.querySelectorAll("[data-nav-link]");
const navbarToggler = document.querySelector("[data-nav-toggler]");

const toggleNav = function () {
  navbar.classList.toggle("active");
  navbarToggler.classList.toggle("active");
}

addEventOnElem(navbarToggler, "click", toggleNav);

const closeNav = function () {
  navbar.classList.remove("active");
  navbarToggler.classList.remove("active");
}

addEventOnElem(navbarLinks, "click", closeNav);



/**
 * header active
 */

const header = document.querySelector("[data-header]");
const mainLogo = document.querySelector(".main-logo");



window.addEventListener("scroll", function () {
  if (window.scrollY >= 100) {
    header.classList.add("active");
    mainLogo.style.width = "7%";
  } else {
    header.classList.remove("active");
    mainLogo.style.width = ""; // or set to original width like "20%" if needed
  }
});


document.addEventListener('DOMContentLoaded', () => {
    // Select all doctor card images
    const doctorImages = document.querySelectorAll('.doctor-card .card-banner img');

    // Create modal elements
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'none';
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
    modal.style.zIndex = '1000';
    modal.style.justifyContent = 'center';
    modal.style.alignItems = 'center';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    modalContent.style.position = 'relative';
    modalContent.style.maxWidth = '90%';
    modalContent.style.maxHeight = '90%';

    const modalImage = document.createElement('img');
    modalImage.style.maxWidth = '100%';
    modalImage.style.maxHeight = '100%';
    modalImage.style.objectFit = 'contain';

    const closeButton = document.createElement('span');
    closeButton.innerHTML = '×';
    closeButton.style.position = 'absolute';
    closeButton.style.top = '10px';
    closeButton.style.right = '20px';
    closeButton.style.color = '#fff';
    closeButton.style.fontSize = '30px';
    closeButton.style.cursor = 'pointer';

    // Append elements
    modalContent.appendChild(modalImage);
    modalContent.appendChild(closeButton);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    // Function to open modal with image
    const openModal = (imageSrc, imageAlt) => {
        modalImage.src = imageSrc;
        modalImage.alt = imageAlt;
        modal.style.display = 'flex';
    };

    // Handle image interactions
    doctorImages.forEach(image => {
        image.style.cursor = 'pointer';

        let touchStartX = 0;
        let touchStartY = 0;
        let isDragging = false;
        const moveThreshold = 10; // Pixels to move before considering it a drag

        // Handle mouse click
        image.addEventListener('click', (e) => {
            e.preventDefault();
            if (!isDragging) {
                openModal(image.src, image.alt);
            }
        });

        // Handle touch start
        image.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
            isDragging = false;
        }, { passive: true });

        // Handle touch move
        image.addEventListener('touchmove', (e) => {
            const touch = e.touches[0];
            const deltaX = Math.abs(touch.clientX - touchStartX);
            const deltaY = Math.abs(touch.clientY - touchStartY);
            if (deltaX > moveThreshold || deltaY > moveThreshold) {
                isDragging = true;
            }
        }, { passive: true });

        // Handle touch end
        image.addEventListener('touchend', (e) => {
            e.preventDefault();
            if (!isDragging) {
                openModal(image.src, image.alt);
            }
        });
    });

    // Close modal on close button click
    closeButton.addEventListener('click', (e) => {
        e.stopPropagation();
        modal.style.display = 'none';
        modalImage.src = ''; // Clear image source when closing
    });

    // Close modal on outside click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            modalImage.src = ''; // Clear image source when closing
        }
    });

    // Close modal on Escape key press
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            modal.style.display = 'none';
            modalImage.src = ''; // Clear image source when closing
        }
    });
});


const scrollContainer = document.getElementById('scrollContainer');
let isDown = false;
let startX;
let scrollLeft;

// Mouse events
scrollContainer.addEventListener('mousedown', (e) => {
    isDown = true;
    scrollContainer.classList.add('dragging');
    startX = e.pageX - scrollContainer.offsetLeft;
    scrollLeft = scrollContainer.scrollLeft;
    scrollContainer.style.scrollBehavior = 'auto';
});

scrollContainer.addEventListener('mouseleave', () => {
    isDown = false;
    scrollContainer.classList.remove('dragging');
    scrollContainer.style.scrollBehavior = 'smooth';
});

scrollContainer.addEventListener('mouseup', () => {
    isDown = false;
    scrollContainer.classList.remove('dragging');
    scrollContainer.style.scrollBehavior = 'smooth';
});

scrollContainer.addEventListener('mousemove', (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - scrollContainer.offsetLeft;
    const walk = (x - startX) * 2; // Scroll speed multiplier
    scrollContainer.scrollLeft = scrollLeft - walk;
});

// Touch events for mobile (already working, but ensuring compatibility)
scrollContainer.addEventListener('touchstart', (e) => {
    startX = e.touches[0].pageX - scrollContainer.offsetLeft;
    scrollLeft = scrollContainer.scrollLeft;
});

scrollContainer.addEventListener('touchmove', (e) => {
    if (!startX) return;
    const x = e.touches[0].pageX - scrollContainer.offsetLeft;
    const walk = (x - startX) * 2;
    scrollContainer.scrollLeft = scrollLeft - walk;
});

// Wheel scrolling (horizontal scroll with mouse wheel)
scrollContainer.addEventListener('wheel', (e) => {
    e.preventDefault();
    scrollContainer.scrollLeft += e.deltaY;
});
