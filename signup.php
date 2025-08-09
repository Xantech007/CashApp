<?php
session_start();
if (isset($_SESSION['auth'])) {
    header("Location: users/index");
    exit(0);
}

include('includes/header.php');
include('includes/navbar.php');
// Include the countries file
include('users/inc/countries.php');
?>

<!-- Breadcrumb Area Start -->
<section class="breadcrumb-area extra-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h4 class="title extra-padding">Register</h4>
                <ul class="breadcrumb-list">
                    <li>
                        <a href="index">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li>
                        <span><i class="fas fa-chevron-right"></i></span>
                    </li>
                    <li>
                        <a href="signup">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Area End -->

<!-- Signin Area Start -->
<section class="auth">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10">
                <div class="sign-form">
                    <div class="heading">
                        <h4 class="title">Create account</h4>
                        <p class="subtitle"></p>
                    </div>
                    <style>
                        ::placeholder {
                            color: #ccc !important;
                        }

                        .errors {
                            text-align: center;
                            padding: 7px 0;
                            margin-bottom: 5px;
                            background: linear-gradient(to bottom, #f7941d, #f76b1c);
                        }

                        .reg-text a {
                            color: #f7951d;
                            font-weight: 600;
                        }

                        .reg-text a:hover,
                        .reg-text a:focus {
                            color: #cc6f0e;
                        }

                        select.form-control {
                            color: black;
                            border: 1px solid #ccc;
                            border-radius: 4px;
                            padding: 10px;
                            width: 100%;
                            background: #fff;
                            -webkit-appearance: none;
                            -moz-appearance: none;
                            appearance: none;
                            background-image: url('data:image/svg+xml;utf8,<svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
                            background-repeat: no-repeat;
                            background-position: right 10px center;
                        }

                        select.form-control:focus {
                            border-color: #f7951d;
                            outline: none;
                            box-shadow: 0 0 5px rgba(247, 149, 29, 0.3);
                        }

                        select.form-control option {
                            color: black;
                        }

                        select.form-control option:disabled {
                            color: #ccc;
                        }
                    </style>
                    <form class="form-group mb-0" action="codes/signup" method="POST" enctype="multipart/form-data">
                        <?php  
                        if (isset($_SESSION['error'])) { ?>
                            <div class="errors"><?= htmlspecialchars($_SESSION['error']) ?></div>
                        <?php } unset($_SESSION['error']); ?>

                        <input class="form-control" type="text" name="name" placeholder="Enter your Name" style="color:black" required>
                        <input class="form-control" type="email" name="email" placeholder="Email Address" style="color:black" required>
                        <input class="form-control" type="password" name="password" placeholder="Password" style="color:black" required>
                        <select class="form-control" name="country" id="countrySelect" required>
                            <option value="" disabled selected>Select your country</option>
                            <?php
                            foreach ($countries as $country) {
                                echo '<option value="' . htmlspecialchars($country) . '">' . htmlspecialchars($country) . '</option>';
                            }
                            ?>
                        </select>
                        <input class="form-control" type="text" readonly name="ref" placeholder="Referred By" style="color:black" value="<?php if (isset($_GET['affiliate-link'])) { echo htmlspecialchars($_GET['affiliate-link']); } ?>">
                        <button class="base-btn1" type="submit" name="register">Create Account</button>
                        <p class="reg-text text-center mb-0">Already have an account? <a href="signin">LogIn</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Signin Area End -->

<script>
// Country name normalization map to handle variations
const countryNameMap = {
    'United States of America': 'United States',
    'United Kingdom of Great Britain and Northern Ireland': 'United Kingdom',
    'Congo, Republic of the': 'Congo (Congo-Brazzaville)',
    'Democratic Republic of Congo': 'Democratic Republic of the Congo',
    'Czech Republic': 'Czechia (Czech Republic)',
    'Swaziland': 'Eswatini',
    'Macedonia': 'North Macedonia',
    'Vatican': 'Vatican City',
    'Timor Leste': 'Timor-Leste',
    'Cape Verde': 'Cabo Verde',
    'Myanmar (Burma)': 'Myanmar',
    'Brunei Darussalam': 'Brunei',
    'Palestinian Territory': 'Palestine',
    'South Korea (Republic of Korea)': 'South Korea',
    'North Korea (DPRK)': 'North Korea',
    'Korea, Republic of': 'South Korea',
    'Korea, Democratic People\'s Republic of': 'North Korea'
};

function setCountryInDropdown(country) {
    const countrySelect = document.getElementById('countrySelect');
    // Normalize country name
    const normalizedCountry = countryNameMap[country] || country;
    
    // Find matching option (case-insensitive)
    const options = Array.from(countrySelect.options);
    const matchingOption = options.find(option => 
        option.value.toLowerCase() === normalizedCountry.toLowerCase() || 
        option.text.toLowerCase() === normalizedCountry.toLowerCase()
    );

    if (matchingOption) {
        countrySelect.value = matchingOption.value;
        console.log('Country set to:', matchingOption.value);
    } else {
        console.warn('Country not found in dropdown:', normalizedCountry);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Check if Geolocation is supported
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                console.log('Geolocation coords:', latitude, longitude);

                // Try Nominatim for reverse geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=3&addressdetails=1`, {
                    headers: {
                        'User-Agent': 'YourAppName/1.0 (your.email@example.com)' // Replace with your app name and contact
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const country = data.address?.country;
                    if (country) {
                        console.log('Nominatim returned country:', country);
                        setCountryInDropdown(country);
                    } else {
                        console.warn('No country found in Nominatim response:', data);
                        // Fallback to IP-based detection
                        fetchIpCountry();
                    }
                })
                .catch(error => {
                    console.error('Nominatim error:', error);
                    // Fallback to IP-based detection
                    fetchIpCountry();
                });
            },
            function (error) {
                console.error('Geolocation error:', error.message);
                // Fallback to IP-based detection
                fetchIpCountry();
            }
        );
    } else {
        console.warn('Geolocation not supported by browser.');
        // Fallback to IP-based detection
        fetchIpCountry();
    }

    // Fallback: Fetch country using IP-based API (ipapi.co)
    function fetchIpCountry() {
        fetch('https://ipapi.co/json/')
            .then(response => response.json())
            .then(data => {
                const country = data.country_name;
                if (country) {
                    console.log('IP API returned country:', country);
                    setCountryInDropdown(country);
                } else {
                    console.warn('No country found in IP API response:', data);
                }
            })
            .catch(error => {
                console.error('IP API error:', error);
            });
    }
});
</script>

<?php include('includes/footer.php') ?>
