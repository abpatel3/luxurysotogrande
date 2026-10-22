$('.recent-blogs').slick({
    dots: false,
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
        {
            breakpoint: 1024,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: true,
            }
        },
        {
            breakpoint: 600,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        },
        {
            breakpoint: 480,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
    ]
});

// document.getElementById('capture-product').addEventListener('click', function() {
//     var captureElement = document.getElementById('capture-target'); // Element to capture
//     console.log('Capture button clicked!'); // Message when button is clicked
//
//     // Use html2canvas to capture the screenshot
//     html2canvas(captureElement).then(function(canvas) {
//         console.log('Screenshot captured!'); // Message when screenshot is ready
//
//         // Convert canvas to image data URL
//         var image = canvas.toDataURL();
//         console.log('Image data URL:', image); // Log image data URL
//
//         // Create an image element to display the captured screenshot
//         var imgElement = document.createElement('img');
//         imgElement.src = image;
//         document.body.appendChild(imgElement); // Add the image to the page
//         console.log('Image added to the page!'); // Message when image is added to page
//
//         // Create a link element to allow image download
//         var downloadLink = document.createElement('a');
//         downloadLink.href = image; // Set the href to the image data URL
//         downloadLink.download = 'screenshot.png'; // Set the default file name
//         downloadLink.textContent = 'Download Image'; // Link text
//         document.body.appendChild(downloadLink); // Add the download link to the page
//         console.log('Download link added to the page!'); // Message when link is added
//
//         // Optionally, automatically trigger the download:
//         downloadLink.click();
//         console.log('Download started!'); // Message when download is triggered
//     }).catch(function(error) {
//         console.error('Error capturing screenshot:', error); // Log any errors
//     });
// });



jQuery(document).ready(function($) {
    // Initialize the datepicker
    $(".datepicker").datepicker({
        dateFormat: 'yy-mm-dd' // You can change the format as per your requirement
    });
});




window.almComplete = function(alm){
	const cardSliders = document.querySelectorAll('.card-slider');
    if (cardSliders) {
        cardSliders.forEach( glide => {
            new Glide(glide, {
                type: 'carousel',
                hoverpause: true,
                gap: 0
            }).mount()
        } );
    }
}


document.addEventListener("DOMContentLoaded", function () {
    var slider = document.getElementById("price-slider");

    noUiSlider.create(slider, {
        start: [0, 20000000], // Default values
        connect: true,
        range: {
            min: 0,
            max: 20000000
        },
        step: 50000,
        tooltips: true,
        format: {
            to: function(value) {
                return value.toLocaleString() + "€"; // Add comma for thousands
            },
            from: function(value) {
                return Number(value.replace("€", "").replace(/,/g, "")); // Remove commas when parsing
            }
        }
    });
	
	// ✅ Set height from JS
    slider.style.height = "9px"; // Change to whatever height you want
    slider.querySelector(".noUi-connect").style.height = "9px";
    slider.querySelectorAll(".noUi-handle").forEach(function(handle){
        handle.style.width = "12px";
    	handle.style.height = "21px";
    });

    var priceValue = document.getElementById("price-value");

    slider.noUiSlider.on("update", function (values) {
        // Display the price values with €
        priceValue.innerHTML = values.join(" - ");

        // Set the hidden input values without the €
        document.getElementById('price_min').value = values[0].replace("€", "").replace(/,/g, "");
        document.getElementById('price_max').value = values[1].replace("€", "").replace(/,/g, "");
    });
});









/**
 * Intialize sliders if any exist on the page
 */
const sliders = document.querySelectorAll('.slider');
if (sliders) {
    sliders.forEach( glide => {
        new Glide(glide, {
            type: 'carousel',
            autoplay: 5000,
            hoverpause: true,
            gap: 0
        }).mount()
    } );
}

/**
 * Intialize property carousel if any exist on the page
 */
const propertyCarousels = document.querySelectorAll('.property-carousel');
if (propertyCarousels) {
    propertyCarousels.forEach( glide => {
        new Glide(glide, {
            type: 'carousel',
            autoplay: 5000,
            hoverpause: true,
            perView: 2,
            breakpoints: {
               640: {
                perView: 1
               }
            }
        }).mount()
    } );
}

/**
 * Intialize sliders if any exist on the page
 */
const newsCarousels = document.querySelectorAll('.news-carousel');
if (newsCarousels) {
    newsCarousels.forEach( glide => {
        new Glide(glide, {
            type: 'carousel',
            autoplay: 5000,
            hoverpause: true,
            perView: 3,
            breakpoints: {
               640: {
                perView: 1
               }
            }
        }).mount()
    } );
}

/**
 * Initialize Testimonials slider
 */
const testimonialSliders = document.querySelectorAll('.testimonials-slider');
if (testimonialSliders) {
    testimonialSliders.forEach( glide => {
        new Glide(glide, {
            type: 'carousel',
            autoplay: 7000,
            hoverpause: true,
            perView: 1,
            gap: 0,
            animationDuration: 600
        }).mount();
    } );
}


/**
 * Initialize AOS
 */


// AOS.init({
//     disable: function() {
//         var maxWidth = 992;
//         return window.innerWidth < maxWidth;
//     }
// });

/**
 * Get the number of days selected in the calendar
 */


if (document.querySelector('.wpbs-form-fields')) {
    const wpbsFormFields = document.querySelector('.wpbs-form-fields');
    const wpbsSelectedDates = document.querySelector('.wpbs-form-selected-dates');
    const wpbsMainWrapper = document.querySelector('.wpbs-main-wrapper');
    const quote = document.querySelector('.quote p');
    const quoteInput = document.querySelector('.quote-input input');
    const wpbsDates = document.querySelectorAll('.wpbs-date');
    const wpbsNext = document.querySelector('.wpbs-next');
    const bookingFormContainer = document.querySelector('.booking-form-container');

    const quoteHTML = document.createElement('div');
    quoteHTML.innerHTML = ``;
    wpbsSelectedDates.after(quoteHTML);
    const mutationObserver = new MutationObserver(entries => {
        entries.forEach(entry => {
            if (entry.addedNodes[0].classList.contains('wpbs-pricing-table')) {
                let startDate = wpbsSelectedDates.querySelector('.wpbs-form-field-start-date .wpbs-form-field-input').innerText;
                let endDate = wpbsSelectedDates.querySelector('.wpbs-form-field-end-date .wpbs-form-field-input').innerText;
                let title = document.querySelector('.property-title').innerText;
                let wpbsPricingTable = document.querySelector('.wpbs-pricing-table');
                let totalNights = wpbsPricingTable.querySelector('.wpbs-price').innerText;
                let lineItemQty = wpbsPricingTable.querySelector('.wpbs-line-item-quantity').innerText;
                let total = parseFloat(totalNights.split(',').join('').slice(0, -1)) + parseFloat(bookingFormContainer.dataset.cleaningFee);
                let dailyPrice = document.querySelector('.wpbs-daily-price').innerText;

                quoteHTML.innerHTML = `
                    <table class="w-full mb-5">
                        <tr>
                            <td class="font-bold" colspan="2">${title}</td>
                        </tr>
                        <tr>
                            <td>Start Date</td>
                            <td class="text-right">${startDate}</td>
                        </tr>
                        <tr>
                            <td>End Date</td>
                            <td class="text-right">${endDate}</td>
                        </tr>
                        <tr>
                            <td>${dailyPrice} ${lineItemQty}</td>
                            <td class="text-right">${totalNights}</td>
                        </tr>
                        <tr>
                            <td>Cleaning Fee</td>
                            <td class="text-right">${bookingFormContainer.dataset.cleaningFee}&euro;</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Total</td>
                            <td class="text-right font-bold">${total}&euro;</td>
                        </tr>
                        <tr>
                            <td>Security Deposit</td>
                            <td class="text-right">${bookingFormContainer.dataset.securityDeposit}&euro;</td>
                        </tr>
                    </table>
                `;

                quoteInput.value = total;
            }
        });
    });

    const wpbsTotalPrice = document.querySelector('.wpbs-total-price');

    mutationObserver.observe(wpbsTotalPrice, { childList: true, subtree: true });
}

// $(function() {
//     var $container = $('.isotope').isotope({
//         itemSelector: '.element-item',
//         layoutMode: 'fitRows',
//         getSortData: {
//             name: '.name',
//             symbol: '.symbol',
//             number: '.number parseInt',
//             category: '[data-category]',
//             weight: function(itemElem) {
//                 var weight = $(itemElem).find('.weight').text();
//                 return parseFloat(weight.replace(/[\(\)]/g, ''));
//             }
//         }
//     });
//
//     // Click event for sorting buttons
//     $('.sorter button').on('click', function() {
//         // Remove the 'active' class from all buttons
//         $('.sorter button').removeClass('active-btn');
//
//         // Add the 'active' class to the clicked button
//         $(this).addClass('active-btn');
//
//         var sortValue = $(this).attr('data-sort-value');
//         var sortDirection = $(this).attr('data-sort-direction');
//         sortDirection = sortDirection == 'asc';
//         $container.isotope({ sortBy: sortValue, sortAscending: sortDirection });
//     });
//
//     // Click event for filtering checkboxes
//     $('.filter-property-features input[type="checkbox"]').on('change', function() {
//         var filters = []; // Array to store selected filters
//
//         // Get selected filters
//         $('.filter-property-features input[type="checkbox"]:checked').each(function() {
//             filters.push($(this).val());
//         });
//
//         // Combine filters using OR logic
//         var filterValue = filters.length ? filters.join(',') : '*';
//
//         // Filter items using Isotope
//         $container.isotope({ filter: filterValue });
//     });
// });


// jQuery(document).ready(function ($) {
//     var $grid = $('.new-isotope').isotope({
//         itemSelector: '.element-item',
//         layoutMode: 'fitRows',
//     });
//
//     // Store filters for multiple sorting
//     var filters = {};
//
//     // Property feature filter
//     $('.property_feature-terms').on('change', function () {
//         var $this = $(this);
//         var group = $this.attr('id'); // Get the filter group (e.g., bedrooms, features)
//         filters[group] = $this.val(); // Store the selected filter value
//
//         // Concatenate filters
//         var filterValue = Object.values(filters)
//             .filter(function (value) {
//                 return value && value !== '*'; // Ignore unselected or "All" options
//             })
//             .join('');
//
//         $grid.isotope({ filter: filterValue || '*' });
//     });
//
//     // Quicksearch functionality
//     // Price range filter
//     // Quicksearch functionality
// // Price range filter
//     // Quicksearch functionality
// // Price range filter
// // Quicksearch functionality
// // Price range filter with dual-range slider
//     $('#price-from-range, #price-to-range').on('input', function () {
//         var fromPrice = parseFloat($('#price-from-range').val()) || 10000;
//         var toPrice = parseFloat($('#price-to-range').val()) || 20000000;
//
//         // Ensure the "from" value is never higher than the "to" value
//         if (fromPrice > toPrice) {
//             fromPrice = toPrice;
//             $('#price-from-range').val(fromPrice);
//         }
//
//         // Update the displayed values
//         $('#price-from').text(fromPrice.toLocaleString());
//         $('#price-to').text(toPrice.toLocaleString());
//
//         // Filter the items based on the price range
//         $grid.isotope({
//             filter: function () {
//                 var itemPrice = parseFloat($(this).attr('data-price')) || 0;
//                 return itemPrice >= fromPrice && itemPrice <= toPrice;
//             }
//         });
//     });
//
//
//
//     // Quicksearch filter
//     $('.quicksearch').on('keyup', function () {
//         var searchValue = $(this).val().toLowerCase();
//
//         $grid.isotope({
//             filter: function () {
//                 var text = $(this).text().toLowerCase();
//                 return text.indexOf(searchValue) !== -1;
//             }
//         });
//     });
// });

function getVals(){
    // Get slider values
    let parent = this.parentNode;
    let slides = parent.getElementsByTagName("input");
    let slide1 = parseFloat( slides[0].value );
    let slide2 = parseFloat( slides[1].value );
    // Neither slider will clip the other, so make sure we determine which is larger
    if( slide1 > slide2 ){ let tmp = slide2; slide2 = slide1; slide1 = tmp; }

    let displayElement = parent.getElementsByClassName("rangeValues")[0];
    displayElement.innerHTML = "$" + slide1 + " - $" + slide2;
}

window.onload = function(){
    // Initialize Sliders
    let sliderSections = document.getElementsByClassName("range-slider");
    for( let x = 0; x < sliderSections.length; x++ ){
        let sliders = sliderSections[x].getElementsByTagName("input");
        for( let y = 0; y < sliders.length; y++ ){
            if( sliders[y].type ==="range" ){
                sliders[y].oninput = getVals;
                // Manually trigger event first time to display values
                sliders[y].oninput();
            }
        }
    }
}


jQuery(document).ready(function ($) {
    $('#sorting-properties').on('change', function () {
        let sorting = $(this).val();
        let url = new URL(window.location.href);
        url.searchParams.set('order', sorting);
        window.location.href = url.toString();
    });
});

$(".openFilter").click(function(){
    $(".filters-sort__container").toggle('fast');
});


$(document).ready(function() {
    $('.select-2').select2({
        width: '100%',
    });
})



$(document).ready(function() {
    $('.search-bar select').select2({
        width: '100%',
    });
})


jQuery(document).ready(function($) {
    $('#load-more-properties').on('click', function() {
        var offset = $(this).data('offset');
        var button = $(this);

        $.ajax({
            url: loadMoreProps.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_properties',
                offset: offset
            },
            beforeSend: function() {
                button.prop('disabled', true);
                button.text('Loading...');
            },
            success: function(response) {
                if(response) {
                    $('.filtered-properties').append(response);
                    button.data('offset', offset + 5);
                    button.prop('disabled', false);
                    button.text('Load More');
                } else {
                    button.text('No More Properties');
                    button.prop('disabled', true);
                }
            }
        });
    });
});

$(function () {
    $(".properties-item-column").slice(0, 4).show();
    $("body").on('click touchstart', '.load-more', function (e) {
        e.preventDefault();
        $(".properties-item-column:hidden").slice(0, 4).slideDown();

        // Reinitialize Slick Slider
        $(".properties-item__gallery-wrapper").slick('setPosition');

        if ($(".properties-item-column:hidden").length == 0) {
            $(".load-more").css('visibility', 'hidden');
        }
        $('html,body').animate({
            scrollTop: $(this).offset().top
        }, 1000);
    });
});




$(document).ready(function() {
    $("#btn-open-closed-status").click(function() {
        $(this).toggleClass('btn-open-closed-active');
        $('#list-status').toggle("slow"); // Toggle the visibility of the element
    });
});

$(document).ready(function() {
    $("#btn-open-closed-area").click(function() {
        $(this).toggleClass('btn-open-closed-active');
        $('#list-area').toggle("slow"); // Toggle the visibility of the element
    });
});



$(document).ready(function() {
    $("#btn-open-closed-sale-sub-1").click(function() {
        $(this).toggleClass('btn-open-closed-active');
        $('#list-sale-sub-1').toggle("slow"); // Toggle the visibility of the element
    });
});

$(document).ready(function() {
    $("#btn-open-closed-sale-sub-2").click(function() {
        $(this).toggleClass('btn-open-closed-active');
        $('#list-sale-sub-2').toggle("slow"); // Toggle the visibility of the element
    });
});

$(document).ready(function() {
    $("#btn-open-closed-3").click(function() {
        $(this).toggleClass('btn-open-closed-active');
        $('#list-3').toggle("slow"); // Toggle the visibility of the element
    });
});

$(document).ready(function($) {
    $('.faq__item-btn').on('click', function() {
        $(this).toggleClass('faq__item-btn-active');
        $(this).next('.faq__item-text').slideToggle('slow');
    });
});


$(document).ready(function(){
    // Get IDs from #list-status and add class to corresponding elements
    $('#list-status').text().trim().split(' ').forEach(function(id){
        $('#' + id).addClass('property-page-items-section');
    });
});

$(document).ready(function($) {
    $('.faq-item__title').on('click', function() {
        $(this).toggleClass('faq-item__title-active');
        $(this).next('.faq-item__content').slideToggle('slow');
    });
});



$(document).ready(function(){
    $('.properties-item__gallery-wrapper').slick({
        dots: false,
        arrows: true,
        infinite: true,
        fade: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        // appendArrows: function(slider, $element) {
        //     return $element.closest('.properties-item__gallery').find('.custom-arrows');
        // },
        // prevArrow: function(slider, $element) {
        //     return $element.closest('.properties-item__gallery').find('.custom-arrows-prev');
        // },
        // nextArrow: function(slider, $element) {
        //     return $element.closest('.properties-item__gallery').find('.custom-arrows-next');
        // }
    });
});




$(document).ready(function() {
    // Handle click event on the element with ID "OpenForm"
    $("#OpenForm").click(function() {
        // Toggle the visibility of the element with ID "SearchForm" with a slide animation (fast)
        $("#SearchForm").slideToggle("fast");

        // Toggle the class "active-form-button" on the element with ID "OpenForm"
        $(this).toggleClass("active-form-button");
    });
});



jQuery(document).ready(function($) {
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 50) { // Change the value to adjust when the class is added
            $('header').addClass('header-scrolled');
        } else {
            $('header').removeClass('header-scrolled');
        }
    });
});

jQuery(document).ready(function($) {
    $('.gallery__list').magnificPopup({
        delegate: 'a.gallery-item', // Selector for gallery items
        type: 'image',
        gallery: {
            enabled: true // Enable gallery mode
        },
    });
});



$('.property-page-images').slick({
    dots: false,
    infinite: true,
    arrows: true,
    speed: 500,
    fade: true,
    cssEase: 'linear',
});

$('.property-page-images').on('click', function() {
    $(this).slick('slickNext');
});

jQuery(document).ready(function($) {
    $('.mobile-menu').on('click', '.menu-item-has-children > a, .mega-dropdown > a', function(e) {
        var $submenu = $(this).siblings('.sub-menu');
        if ($submenu.length) {
            e.preventDefault();
            e.stopPropagation();
            $submenu.slideToggle(250);
            $(this).parent().toggleClass('open');
        }
    });
});


$('.card-top').slick({
    dots: false,
    infinite: true,
    arrows: true,
    speed: 500,
    fade: true,
    cssEase: 'linear',
});


jQuery(document).ready(function($) {
    $(".go-back").click(function() {
        window.history.back();
    });
});


console.log(html2canvas); // Should not be undefined

// Mega Menu Interaction for Luxury Sotogrande Header (Dynamic Multi-Mega Support)
jQuery(document).ready(function($) {
    let megaTimeout;
    const $megaPanels = $('.header-megamenu-panel');
    const $primaryNavItems = $('.header-primary-nav > li');

    if (!$megaPanels.length) return;

    function closeAllMega() {
        $megaPanels.removeClass('is-active');
        $primaryNavItems.removeClass('megamenu-trigger-active');
    }

    // Hover on any menu item
    $primaryNavItems.on('mouseenter', function() {
        clearTimeout(megaTimeout);
        const $this = $(this);
        const menuId = $this.data('menu-id');

        // Check if this item has its own dynamic mega panel
        let $targetPanel = $('#megamenu-panel-' + menuId);

        // Fallback for default static panel if single mega menu
        if (!$targetPanel.length && ($this.hasClass('has-megamenu') || $this.is(':first-child'))) {
            $targetPanel = $('#header-megamenu-panel');
        }

        if ($targetPanel.length && $this.hasClass('has-megamenu')) {
            $megaPanels.not($targetPanel).removeClass('is-active');
            $primaryNavItems.not(this).removeClass('megamenu-trigger-active');
            $targetPanel.addClass('is-active');
            $this.addClass('megamenu-trigger-active');
        } else {
            // Normal link (e.g. Blog, Contact Us) -> close mega panels
            closeAllMega();
        }
    });

    $primaryNavItems.on('mouseleave', function() {
        const menuId = $(this).data('menu-id');
        let $targetPanel = $('#megamenu-panel-' + menuId);
        if (!$targetPanel.length) {
            $targetPanel = $('#header-megamenu-panel');
        }

        megaTimeout = setTimeout(function() {
            if (!$targetPanel.is(':hover')) {
                $targetPanel.removeClass('is-active');
                $('.header-primary-nav > li[data-menu-id="' + menuId + '"]').removeClass('megamenu-trigger-active');
            }
        }, 180);
    });

    $megaPanels.on('mouseenter', function() {
        clearTimeout(megaTimeout);
    });

    $megaPanels.on('mouseleave', function() {
        const $panel = $(this);
        const menuId = $panel.data('menu-id');
        megaTimeout = setTimeout(function() {
            $panel.removeClass('is-active');
            if (menuId) {
                $('.header-primary-nav > li[data-menu-id="' + menuId + '"]').removeClass('megamenu-trigger-active');
            } else {
                $primaryNavItems.removeClass('megamenu-trigger-active');
            }
        }, 180);
    });

    // Close on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            closeAllMega();
        }
    });
});

// Property Hero Banner Slider (Custom Slick Slider with Glassmorphism Card & Control Pill)
jQuery(document).ready(function($) {
    $('.property-hero-slider-wrap').each(function() {
        const $wrap = $(this);
        const $slider = $wrap.find('.property-hero-slider-main');
        if (!$slider.length) return;

        const isAutoplay = $wrap.data('autoplay') !== false && $wrap.data('autoplay') !== 'false';
        const autoplaySpeed = parseInt($wrap.data('autoplay-speed'), 10) || 3000;
        let isManuallyPaused = false;

        $slider.slick({
            dots: false,
            arrows: false,
            infinite: true,
            fade: true,
            speed: 800,
            cssEase: 'cubic-bezier(0.25, 1, 0.5, 1)',
            autoplay: isAutoplay,
            autoplaySpeed: autoplaySpeed,
            pauseOnHover: false,
            pauseOnFocus: false,
            pauseOnDotsHover: false,
            adaptiveHeight: false
        });

        // Sync active dash indicator on slide change
        $slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
            $wrap.find('.property-hero-dash')
                .removeClass('is-active')
                .eq(nextSlide)
                .addClass('is-active');

            // If stopped by user, prevent Slick from restarting autoplay
            if (isManuallyPaused) {
                $slider.slick('slickPause');
            }
        });

        // Click on dash to go to specific slide
        $wrap.on('click', '.property-hero-dash', function(e) {
            e.preventDefault();
            const slideIndex = parseInt($(this).data('slide-index'), 10);
            if (!isNaN(slideIndex)) {
                $slider.slick('slickGoTo', slideIndex);
                if (isManuallyPaused) {
                    $slider.slick('slickPause');
                }
            }
        });

        // Prev / Next button clicks
        $wrap.on('click', '.property-hero-prev', function(e) {
            e.preventDefault();
            $slider.slick('slickPrev');
            if (isManuallyPaused) {
                $slider.slick('slickPause');
            }
        });

        $wrap.on('click', '.property-hero-next', function(e) {
            e.preventDefault();
            $slider.slick('slickNext');
            if (isManuallyPaused) {
                $slider.slick('slickPause');
            }
        });

        // Pause / Play toggle
        $wrap.on('click', '.property-hero-pause-btn', function(e) {
            e.preventDefault();
            const $btn = $(this);

            if (!isManuallyPaused) {
                // User clicks STOP/PAUSE
                isManuallyPaused = true;
                $slider.slick('slickSetOption', 'autoplay', false, false);
                $slider.slick('slickPause');
                $btn.attr('data-state', 'paused');
                $btn.find('.property-hero-icon-pause').addClass('hidden');
                $btn.find('.property-hero-icon-play').removeClass('hidden');
                $btn.attr('aria-label', 'Resume slider autoplay');
            } else {
                // User clicks PLAY to resume auto-sliding
                isManuallyPaused = false;
                $slider.slick('slickSetOption', 'autoplay', true, false);
                $slider.slick('slickPlay');
                $btn.attr('data-state', 'playing');
                $btn.find('.property-hero-icon-pause').removeClass('hidden');
                $btn.find('.property-hero-icon-play').addClass('hidden');
                $btn.attr('aria-label', 'Pause slider autoplay');
            }
        });
    });
});

