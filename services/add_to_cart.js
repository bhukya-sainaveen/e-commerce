$(document).ready(function() {
    // Function to handle the click event for each "Add to Cart" button
    function handleAddToCartClick(e) {
        e.preventDefault();
        var productID = $(this).data("product-id");

        // Store a reference to the button for later use
        var addButton = $(this);

        // Check if the button has been clicked before for this product
        if (!addButton.data("button-clicked")) {
            // AJAX request to add the product to the cart
            $.ajax({
                url: 'services/process_cart',
                method: 'POST',
                data: { id: productID },
                success: function(response) {
                    // Handle success response
                    if (response['success']) {
                        addButton.text("Added").attr("disabled", true);

                        // Simulating an asynchronous operation with setTimeout
                        setTimeout(function() {
                            // Change the button back to "Go to Cart" after 1 second
                            addButton.text("Go to Cart").removeAttr("disabled");
                            addButton.removeClass("add-to-cart").addClass("go-to-cart");
                            addButton.removeClass("btn-secondary").addClass("btn-warning")
                            addButton.data("button-clicked", true);
                        }, 200); // Adjust the delay time as needed
                    } else if(typeof(response['message']) === 'undefined') {
                        location.reload();
                    } else {
                        console.log(response['message']);
                    }
                },
                error: function(error) {
                    // Handle error response
                    console.error('Error adding product to cart');
                }
            });
        } else {
            // If the button has been clicked before, go to the cart directly
            window.location.href = "services/cart";
        }
    }

    // Attach the click event handler to all "Add to Cart" buttons
    $(".add-to-cart").on("click", handleAddToCartClick);

    // Updated click event for the dynamically created "Go to Cart" button
    $(document).on("click", ".go-to-cart", function(e) {
        e.preventDefault();
        window.location.href = "services/cart";
    });
});
