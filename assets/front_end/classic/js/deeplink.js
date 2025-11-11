
var android_app_store_link = $('#android_app_store_link').val();
var ios_app_store_link = $('#ios_app_store_link').val();
var host = $('#host').val();
var scheme = $('#scheme').val();
var app_name = $('#app_name').val();
var doctor_brown = $('#doctor_brown').val();
// const pathName = window.location.pathname;

// alert('scheme+'://'+host+pathName);

if (doctor_brown != '') {
    $(document).ready(function () {
        // Function to check if the device is mobile or tablet
        function isMobileOrTablet() {
            return true;
            return window.matchMedia("(max-width: 1024px)").matches;
        }
        function openInApp(pathName) {
            // var appScheme = 'eshop://vendoreshop.wrteam.co.in'+pathName;
            // var androidAppStoreLink = 'https://play.google.com/store/apps/details?id=eShop.multivendor.customer';
            // var iosAppStoreLink = 'https://apps.apple.com/app/eshop/idYOUR_APP_ID';
            var appScheme = scheme+'://'+host+pathName;
            var androidAppStoreLink = android_app_store_link;
            var iosAppStoreLink = ios_app_store_link;
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            var isAndroid = /android/i.test(userAgent);
            var isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
            var appStoreLink = isAndroid ? androidAppStoreLink : (isIOS ? iosAppStoreLink : androidAppStoreLink);
            // Attempt to open the app
            window.location.href = appScheme;
            // Set a timeout to check if app opened
            setTimeout(function() {
                if (document.hidden || document.webkitHidden) {
                    // App opened successfully
                } else {
                    // App is not installed, ask user if they want to go to app store
                    if (confirm(app_name + "app is not installed. Would you like to download it from the app store?")) {
                        window.location.href = appStoreLink;
                    }
                }
            }, 1000);
        }
        // Only add the bottom sheet and its functionality if it's a mobile or tablet device
        if(document.getElementById("share_slug") !==null ){
            if (isMobileOrTablet()) {
                // Add the bottom sheet HTML
                // const pathName = window.location.pathname;
                const pathName = window.location.pathname + window.location.search;

                document.getElementsByClassName("deeplink_wrapper")[0].innerHTML = `
                <div class="bottom-sheet p-4" id="bottomSheet">
                    <h5>Open in App</h5>
                    <p>Get a better experience by using our mobile app!</p>
                    <button class="btn btn-outline-secondary w-100 mb-2" onclick="openApp()">Open in APP</button>
                    <button class="btn btn-outline-danger w-100" onclick="hideBottomSheet()">Close</button>
                </div>
                ` + document.getElementsByClassName("deeplink_wrapper")[0].innerHTML;
                //  <button class="btn btn-outline-secondary w-100" onclick="hideBottomSheet()">Close</button>
                // <button class="btn btn-primary w-100 mb-2" onclick="openApp()>Open in App</button>
                // Add the CSS for the bottom sheet
                const style = document.createElement('style');
                style.textContent = `
                    .bottom-sheet {
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        right: 0;
                        background-color: #fff;
                        border-top-left-radius: 15px;
                        border-top-right-radius: 15px;
                        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                        transform: translateY(100%);
                        transition: transform 0.3s ease-out;
                        z-index: 1050;
                    }
                    .bottom-sheet.show {
                        transform: translateY(0);
                    }
                    @media (min-width: 1025px) {
                        .bottom-sheet {
                            display: none;
                        }
                    }
                `;
                document.head.appendChild(style);
                // Define the toggle and hide functions
                window.toggleBottomSheet = function(show = true) {
                    const bottomSheet = document.getElementById('bottomSheet');
                    if (show) {
                        bottomSheet.classList.add('show');
                    } else {
                        bottomSheet.classList.remove('show');
                    }
                }
                window.hideBottomSheet = function() {
                    toggleBottomSheet(false);
                    sessionStorage.setItem('bottomSheetShown', 'true');
                }
                window.openApp = function(){
                    openInApp(pathName);
                }
                // Check if we should show the bottom sheet when the page loads
                if (!sessionStorage.getItem('bottomSheetShown')) {
                    toggleBottomSheet(true);
                }
            }
        }
    });
    
}