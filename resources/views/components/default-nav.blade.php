<header class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Logo and Title Section -->
            <div class="flex items-center gap-3">
                <div class="text-3xl text-gray-900">
                    <i class="ri-shield-keyhole-fill"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 mb-0">Secure Vote Ph</h1>
                    <p class="text-sm text-gray-700 mt-0">Trusted Democracy</p>
                </div>
            </div>

            <!-- Navigation Items -->
            <nav class="hidden md:flex">
                <ul class="flex gap-8">
                    <li>
                        <a href="#geo" class="nav-link font-medium text-gray-900 hover:text-blue-900 focus:text-blue-900 transition-colors duration-200">
                            Geo
                        </a>
                    </li>
                    <li>
                        <a href="#security" class="nav-link font-medium text-gray-900 hover:text-blue-900 focus:text-blue-900 transition-colors duration-200">
                            Security
                        </a>
                    </li>
                    <li>
                        <a href="#analytics" class="nav-link font-medium text-gray-900 hover:text-blue-900 focus:text-blue-900 transition-colors duration-200">
                            Analytics
                        </a>
                    </li>
                    <li>
                        <a href="#faqs" class="nav-link font-medium text-gray-900 hover:text-blue-900 focus:text-blue-900 transition-colors duration-200">
                            FAQs
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-900 hover:text-blue-900 focus:text-blue-900 transition-colors duration-200" aria-label="Toggle menu">
                    <i class="ri-menu-line text-xl transition-transform duration-300"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden overflow-hidden transition-all duration-300 ease-in-out" style="max-height: 0; opacity: 0;">
            <nav class="mt-4 pb-4">
                <ul class="space-y-3">
                    <li>
                        <a href="#geo" class="nav-link block py-2 px-4 font-medium text-gray-900 hover:text-blue-900 hover:bg-gray-100 focus:text-blue-900 focus:bg-gray-100 transition-all duration-200 rounded-lg text-center">
                            Geo
                        </a>
                    </li>
                    <li>
                        <a href="#security" class="nav-link block py-2 px-4 font-medium text-gray-900 hover:text-blue-900 hover:bg-gray-100 focus:text-blue-900 focus:bg-gray-100 transition-all duration-200 rounded-lg text-center">
                            Security
                        </a>
                    </li>
                    <li>
                        <a href="#analytics" class="nav-link block py-2 px-4 font-medium text-gray-900 hover:text-blue-900 hover:bg-gray-100 focus:text-blue-900 focus:bg-gray-100 transition-all duration-200 rounded-lg text-center">
                            Analytics
                        </a>
                    </li>
                    <li>
                        <a href="#faqs" class="nav-link block py-2 px-4 font-medium text-gray-900 hover:text-blue-900 hover:bg-gray-100 focus:text-blue-900 focus:bg-gray-100 transition-all duration-200 rounded-lg text-center">
                            FAQs
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile menu toggle
            const mobileButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileButton) {
                mobileButton.addEventListener('click', function() {
                    const menuIcon = this.querySelector('i');
                    const isOpen = mobileMenu.style.maxHeight !== '0px' && mobileMenu.style.maxHeight !== '';

                    if (!isOpen) {
                        mobileMenu.style.maxHeight = '300px';
                        mobileMenu.style.opacity = '1';
                        menuIcon.classList.remove('ri-menu-line');
                        menuIcon.classList.add('ri-close-line');
                        menuIcon.style.transform = 'rotate(180deg)';
                    } else {
                        mobileMenu.style.maxHeight = '0px';
                        mobileMenu.style.opacity = '0';
                        menuIcon.classList.remove('ri-close-line');
                        menuIcon.classList.add('ri-menu-line');
                        menuIcon.style.transform = 'rotate(0deg)';
                    }
                });
            }

            // Smooth scroll functionality
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const headerHeight = document.querySelector('header').offsetHeight;
                        const targetPosition = targetElement.offsetTop - headerHeight - 20;

                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });

                        // Close mobile menu if open
                        if (mobileMenu && mobileMenu.style.maxHeight !== '0px' && mobileMenu.style.maxHeight !== '') {
                            mobileButton.click();
                        }
                    }
                });
            });
        });
    </script>
</header>
