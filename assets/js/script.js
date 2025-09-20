// Toggle Menu
function menutoggle() {
    var MenuItems = document.getElementById("MenuItems");
    if (MenuItems.style.maxHeight == "0px") {
        MenuItems.style.maxHeight = "200px";
    } else {
        MenuItems.style.maxHeight = "0px";
    }
}

// Form Validation
function validateForm() {
    var email = document.forms["registerForm"]["email"].value;
    var password = document.forms["registerForm"]["password"].value;
    var confirm_password = document.forms["registerForm"]["confirm_password"].value;
    
    if (password != confirm_password) {
        alert("Password dan Konfirmasi Password harus sama!");
        return false;
    }
    
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("Format email tidak valid!");
        return false;
    }
    
    return true;
}

// Add to Cart Animation
function addToCartAnimation() {
    var cart = document.querySelector('.cart-icon');
    cart.classList.add('animate');
    setTimeout(function() {
        cart.classList.remove('animate');
    }, 1000);
}

// Product Search
function searchProducts() {
    var input = document.getElementById('searchInput');
    var filter = input.value.toUpperCase();
    var products = document.getElementsByClassName('produk-item');
    
    for (var i = 0; i < products.length; i++) {
        var productName = products[i].getElementsByTagName('h4')[0];
        if (productName.innerHTML.toUpperCase().indexOf(filter) > -1) {
            products[i].style.display = "";
        } else {
            products[i].style.display = "none";
        }
    }
}

// Image Gallery
function changeImage(element) {
    var mainImage = document.getElementById('mainImage');
    mainImage.src = element.src;
}

// Quantity Controls
function increaseQuantity() {
    var quantityInput = document.getElementById('quantity');
    quantityInput.value = parseInt(quantityInput.value) + 1;
}

function decreaseQuantity() {
    var quantityInput = document.getElementById('quantity');
    if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners
    var addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', addToCartAnimation);
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});