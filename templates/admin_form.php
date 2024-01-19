<br><br>

<div class="container text-center">

    <a href="./" class="btn btn-primary">Go Home</a><br><br>

</div>


<div class="container text-center">
    <div class="container px-lg-5" style="border-style:groove;border-color:#1F97FF;">
        <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <br>
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" placeholder="Product name" autocomplete="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
                <span class="text-danger"><?php echo $nameErr; ?></span>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Product description" rows="3" required><?php echo isset($description) ? $description : ''; ?></textarea>
                <span class="text-danger"><?php echo $descErr; ?></span>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <?php
                    $categories = getCategories($conn);
                    foreach ($categories as $cat) {
                        echo "<option value=\"$cat\"";
                        if (isset($category) && $category == $cat) {
                            echo " selected";
                        }
                        echo ">$cat</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" min="1" id="price" name="price" class="form-control" placeholder="1" autocomplete="price" value="<?php echo isset($price) ? $price : ''; ?>" required>
                <span class="text-danger"><?php echo $priceErr; ?></span>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
            </div>

            <?php
            // Assuming $imageName is the name of the previously uploaded image
            if (isset($imageName)) {
                echo '<p>Current Image: ' . $imageName . '</p>';
            }
            ?>

            <button type="submit" class="btn btn-success form-control" style="width:25%;">Submit</button><br><br>
            <br>
        </form>
    </div>
</div>