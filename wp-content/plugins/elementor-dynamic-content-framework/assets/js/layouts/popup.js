/**
 * Popup Layout JavaScript
 *
 * Handles modal popup interactions, navigation, and animations.
 *
 * @package Elementor_Dynamic_Content_Framework
 */

(function() {
    'use strict';

    /**
     * Initialize popup functionality
     */
    function initPopups() {
        const popupWrappers = document.querySelectorAll('.dcf-popup-wrapper');

        popupWrappers.forEach(wrapper => {
            const thumbnails = wrapper.querySelectorAll('.dcf-popup-thumbnail');
            const modal = wrapper.querySelector('.dcf-popup-modal');
            const overlay = wrapper.querySelector('.dcf-popup-overlay');
            const closeBtn = wrapper.querySelector('.dcf-popup-close');
            const prevBtn = wrapper.querySelector('.dcf-popup-prev');
            const nextBtn = wrapper.querySelector('.dcf-popup-next');
            const popupItems = wrapper.querySelectorAll('.dcf-popup-item');

            let currentIndex = 0;

            // Open popup on thumbnail click
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', () => {
                    openPopup(index);
                });
            });

            // Close popup
            if (closeBtn) {
                closeBtn.addEventListener('click', closePopup);
            }

            if (overlay) {
                overlay.addEventListener('click', closePopup);
            }

            // Navigation
            if (prevBtn) {
                prevBtn.addEventListener('click', showPrevious);
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', showNext);
            }

            // Keyboard navigation
            document.addEventListener('keydown', handleKeyboard);

            /**
             * Open popup at specific index
             */
            function openPopup(index) {
                currentIndex = index;
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                showItem(currentIndex);
            }

            /**
             * Close popup
             */
            function closePopup() {
                modal.classList.remove('active');
                document.body.style.overflow = '';
                
                // Hide all items
                popupItems.forEach(item => {
                    item.classList.remove('active');
                });
            }

            /**
             * Show specific item
             */
            function showItem(index) {
                // Hide all items
                popupItems.forEach(item => {
                    item.classList.remove('active');
                });

                // Show current item
                if (popupItems[index]) {
                    popupItems[index].classList.add('active');
                }

                // Update navigation button visibility
                updateNavButtons();
            }

            /**
             * Show previous item
             */
            function showPrevious() {
                currentIndex = (currentIndex - 1 + popupItems.length) % popupItems.length;
                showItem(currentIndex);
            }

            /**
             * Show next item
             */
            function showNext() {
                currentIndex = (currentIndex + 1) % popupItems.length;
                showItem(currentIndex);
            }

            /**
             * Update navigation button visibility
             */
            function updateNavButtons() {
                if (popupItems.length <= 1) {
                    if (prevBtn) prevBtn.style.display = 'none';
                    if (nextBtn) nextBtn.style.display = 'none';
                } else {
                    if (prevBtn) prevBtn.style.display = 'flex';
                    if (nextBtn) nextBtn.style.display = 'flex';
                }
            }

            /**
             * Handle keyboard navigation
             */
            function handleKeyboard(e) {
                if (!modal.classList.contains('active')) {
                    return;
                }

                switch(e.key) {
                    case 'Escape':
                        closePopup();
                        break;
                    case 'ArrowLeft':
                        showPrevious();
                        break;
                    case 'ArrowRight':
                        showNext();
                        break;
                }
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPopups);
    } else {
        initPopups();
    }

    // Re-initialize for Elementor preview
    if (typeof elementorFrontend !== 'undefined') {
        elementorFrontend.hooks.addAction('frontend/element_ready/widget', function($scope) {
            if ($scope.find('.dcf-popup-wrapper').length) {
                initPopups();
            }
        });
    }
})();
