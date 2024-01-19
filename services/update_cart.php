<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
    function updateQuantity(productId, change) {
        var currentQuantity = parseInt($("#quantity" + productId).text());
        var newQuantity = currentQuantity + change;

        // Ensure quantity doesn't go below 0
        if (newQuantity < 0) {
            newQuantity = 0;
        }
        // Call AJAX function to update quantity in the database
        $.post('process_cart', { id: productId, quantity: newQuantity}, function(response) {
            // Handle the response if needed
            if (response['success']) {
                // Delete item if quantity is zero
                if (newQuantity == 0) {
                    location.reload();
                } else {
                    // Update quantity in the HTML
                    $("#quantity" + productId).text(newQuantity);
                    totalPriceChange = parseInt($("#totalPrice" + productId).text()) * (newQuantity - currentQuantity);
                    orderTotal = parseInt($("#orderTotal").text()) + totalPriceChange;
                    $("#orderTotal").text(orderTotal);
                }
            } else if(typeof(response['message']) === 'undefined') {
                location.reload();
            } else {
                $("#error").text(response['message']).addClass("alert-danger");
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            // Handle the case when the POST request fails
            $("#error").text('Error: ' + textStatus + ' - ' + errorThrown).addClass("alert-danger");
        });
    }

    function updateOrderTotal() {
        // Calculate and update the order total
        var orderTotal = 0;
        <?php foreach ($products as $product) : ?>
            var productTotal = parseInt($("#totalPrice<?php echo $product['product_id']; ?>").text()) * parseInt($("#quantity<?php echo $product['product_id']; ?>").text());
            orderTotal += productTotal;
        <?php endforeach; ?>

        // Display the order total
        $("#orderTotal").text(orderTotal);
    }

    // Initial update of the order total
    updateOrderTotal();
</script>