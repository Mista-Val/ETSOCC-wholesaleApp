// Banner Slider Configuration
// const bannerSwiper = new Swiper('.banner-list', {
//   loop: true,
//   autoplay: {
//     delay: 5000,
//     disableOnInteraction: false,
//   },
//   speed: 1500,
//   effect: 'fade',
//   fadeEffect: {
//     crossFade: true
//   },
//   pagination: {
//     el: '.swiper-pagination',
//     clickable: true
//   },
//   navigation: {
//     nextEl: '.swiper-button-next',
//     prevEl: '.swiper-button-prev'
//   },
//   on: {
//     init: function() {
//       // Add zoom class to active slide on init
//       this.slides[this.activeIndex].classList.add('zoom-zoom');
//     },
//     slideChangeTransitionStart: function() {
//       // Remove zoom class from all slides
//       this.slides.forEach(slide => {
//         slide.classList.remove('zoom-zoom');
//       });
//     },
//     slideChangeTransitionEnd: function() {
//       // Add zoom class to new active slide
//       this.slides[this.activeIndex].classList.add('zoom-zoom');
//     }
//   }
// });

// Destination Slider Configuration
// const destinationSwiper = new Swiper('.destination-swiper', {
//   slidesPerView: 1,
//   spaceBetween: 20,
//   loop: true,
//   //centeredSlides: true,
//   autoplay: {
//     delay: 4000,
//     disableOnInteraction: false,
//   },
//   breakpoints: {
//     640: {
//       slidesPerView: 2,
//       spaceBetween: 20,
//     },
//     1024: {
//       slidesPerView: 2.5,
//       spaceBetween: 30,
//     },
//   },
// });


// Header Scroll Effect
document.addEventListener('DOMContentLoaded', function() {
  const header = document.querySelector('.header-bg');
  const scrollThreshold = 50;
  
  // Check initial scroll position
  if (window.scrollY > scrollThreshold) {
    header.classList.add('header-scrolled');
  }
  
  // Add scroll event listener
  window.addEventListener('scroll', function() {
    if (window.scrollY > scrollThreshold) {
      header.classList.add('header-scrolled');
    } else {
      header.classList.remove('header-scrolled');
    }
  });
  
  // Mobile Menu Toggle
  const mobileMenuButton = document.getElementById('mobile-menu-button');
  const mobileMenu = document.getElementById('mobile-menu');
  
  if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', function() {
      mobileMenu.classList.toggle('hidden');
    });
  }
  
  // Close mobile menu when clicking outside
  document.addEventListener('click', function(event) {
    if (mobileMenu && !mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
      mobileMenu.classList.add('hidden');
    }
  });
});


// For Search

// document.addEventListener('DOMContentLoaded', function() {
//   const searchButton = document.getElementById('searchButton');
//   const searchModal = document.getElementById('searchModal');
//   const closeSearch = document.getElementById('closeSearch');
//   const searchInput = document.getElementById('searchInput');

//   // Open modal when search button is clicked
//   searchButton.addEventListener('click', function() {
//      searchModal.classList.remove('hidden');
//      searchModal.classList.add('flex');
//      searchInput.focus();
//      document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
//   });

//   // Close modal when close button is clicked
//   closeSearch.addEventListener('click', function() {
//      searchModal.classList.add('hidden');
//      searchModal.classList.remove('flex');
//      document.body.style.overflow = ''; // Re-enable scrolling
//   });

//   // Close modal when clicking outside the modal content
//   searchModal.addEventListener('click', function(e) {
//      if (e.target === searchModal) {
//         searchModal.classList.add('hidden');
//         searchModal.classList.remove('flex');
//         document.body.style.overflow = ''; // Re-enable scrolling
//      }
//   });

//   // Close modal with Escape key
//   document.addEventListener('keydown', function(e) {
//      if (e.key === 'Escape' && !searchModal.classList.contains('hidden')) {
//         searchModal.classList.add('hidden');
//         searchModal.classList.remove('flex');
//         document.body.style.overflow = ''; // Re-enable scrolling
//      }
//   });
// });



// Function to update header height and set padding for inner page wrapper
function updateHeaderAndPageSpacing() {
  const header = document.querySelector('header');
  const innerPageWarper = document.querySelector('.inner-page-warper');
  
  if (header && innerPageWarper) {
      const headerHeight = header.offsetHeight;
      innerPageWarper.style.paddingTop = `${headerHeight}px`;
  }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
  // Existing search modal code...
  
  
  // Update header spacing on load and resize
  updateHeaderAndPageSpacing();
  
  // Add event listener for window resize
  let resizeTimer;
  window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
          updateHeaderAndPageSpacing();
      }, 250); // Debounce resize events
  });
});

// Select all menu links
const menuLinks = document.querySelectorAll(".select-user-list li a");

menuLinks.forEach(link => {
  link.addEventListener("click", function() {
    // remove active from all
    menuLinks.forEach(l => l.classList.remove("active"));
    // add active to clicked one
    this.classList.add("active");
  });
});

function initOTPInput(selector) {
            const inputs = document.querySelectorAll(selector || ".otp-input");
 
            inputs.forEach((input, idx) => {
                // typing -> go forward
                input.addEventListener("input", (e) => {
                    if (input.value.length > 1) {
                        input.value = input.value.slice(-1); // only last char
                    }
                    if (input.value && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                });
 
                // backspace -> go back
                input.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && !input.value && idx > 0) {
                        inputs[idx - 1].focus();
                    }
                });
 
                // paste -> fill all
                input.addEventListener("paste", (e) => {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData)
                        .getData("text")
                        .slice(0, inputs.length)
                        .split("");
                    paste.forEach((char, i) => {
                        inputs[i].value = char;
                    });
                    inputs[Math.min(paste.length, inputs.length) - 1].focus();
                });
            });
        }
