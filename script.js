let bar = document.getElementById('bar');
let nav = document.getElementById('navbar');
let mobile = document.getElementById('mobile');
let close = document.getElementById('close');

if (bar) {
    bar.addEventListener('click', () => {
        nav.classList.add('active');
        mobile.classList.add('hide'); // মোবাইল cart & bar আইকন হাইড করো
    });
}

if (close) {
    close.addEventListener('click', () => {
        nav.classList.remove('active');
        mobile.classList.remove('hide'); // আবার দেখাও
    });
}






let availableKeywords = [
    't-shirt',
    'polo',
    'cotton',
    'tee treasure',
    'jeans',
    'hoodie',
    'shirt',
    'shoes',
    'cap',
    'jacket',
    'trouser'
];

const resultsBox = document.querySelector(".result-box");
const inputBox = document.getElementById("input-box");
const searchButton = document.querySelector(".search-row button");
const products = document.querySelectorAll(".pro");
const productContainer = document.querySelector(".pro-container");

// সার্চ ইনপুট লেখার সময় সাজেশন দেখানো
inputBox.onkeyup = function () {
    let input = inputBox.value.toLowerCase();
    let result = [];

    if (input.length) {
        result = availableKeywords.filter((keyword) => {
            return keyword.toLowerCase().includes(input);
        }).slice(0, 5); // সর্বোচ্চ ৫টি সাজেশন দেখাবে
    }

    display(result);

    if (result.length) {
        resultsBox.style.display = "block";
        productContainer.style.marginTop = "150px"; // ৫টি সাজেশন এর নিচ থেকে pro-container শুরু হবে
    } else {
        resultsBox.style.display = "none";
        productContainer.style.marginTop = "0"; // কোনো সাজেশন না থাকলে ডিজাইন ঠিক রাখবে
    }
};

function display(result) {
    if (result.length > 0) {
        resultsBox.innerHTML = "<ul>" + result.map((list) => {
            return "<li onclick='selectInput(this)'>" + list + "</li>";
        }).join('') + "</ul>";
    } else {
        resultsBox.innerHTML = "";
    }
}

// ইউজার যখন সাজেশন ক্লিক করবে
function selectInput(listItem) {
    inputBox.value = listItem.innerText;
    resultsBox.style.display = "none";
    productContainer.style.marginTop = "0"; // সাজেশন লুকিয়ে গেলে ডিজাইন ঠিক রাখবে
}

// সার্চ বাটন ক্লিক করলে প্রোডাক্ট ফিল্টার হবে
searchButton.onclick = function () {
    let input = inputBox.value.toLowerCase();
    let found = false; // চেক করার জন্য ভেরিয়েবল
    
    products.forEach((product) => {
        let productName = product.querySelector(".des h5").innerText.toLowerCase();
        let productBrand = product.querySelector(".des span").innerText.toLowerCase();

        if (productName.includes(input) || productBrand.includes(input)) {
            product.style.display = "block";
            found = true; // যদি মিল পাওয়া যায়
        } else {
            product.style.display = "none";
        }
    });

    // যদি কোনো প্রোডাক্ট না মেলে, তখন "No result found" দেখাবে
    if (!found) {
        productContainer.innerHTML = "<h3 style='text-align:center; color:red;'>No result found</h3>";
    }

    resultsBox.style.display = "none"; // সার্চ করার পর সাজেশন লুকিয়ে ফেলবে
    productContainer.style.marginTop = "0"; // ডিজাইন ঠিক রাখবে
};


