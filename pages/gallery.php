<h1>Gallery Page</h1>

<?php
    Messages::getMessage();
?>

<form action="index.php" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label for="image">Image: </label>
        <input type="file" name="image" class="form-control">
    </div>

    <button class="btn btn-primary mt-3" name="action" value="uploadImage">Upload</button>
</form>

