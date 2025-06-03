<h1>Reviews</h1>

<?php
    Messages::getMessage();
?>

<form action="index.php" method="post">
    <div class="form-group mt-3">
        <label for="name">Name: </label>
        <input type="text" name="name" class="form-control" value="<?= OldInput::get('name') ?>">
    </div>

    <div class="form-group mt-3">
        <label for="message">Message: </label>
        <textarea name="message" class="form-control"><?= OldInput::get('message') ?></textarea>
    </div>

    <button class="btn btn-primary mt-3" name="action" value="sendReview">Send</button>
</form>

<!-- 
вивести відгуки нові - на початку

Name      date
Message

 -->

<?php
    $reviews = file_exists("reviews.json") ? array_reverse(json_decode(file_get_contents("reviews.json"), true)) : [];
    $perPage = 3;
    $totalPages = ceil(count($reviews) / $perPage);
    $p = $_GET['p'] ?? 1;

    // $reviews = array_slice($reviews, ($p - 1) * $perPage, $perPage);
    $reviews = array_chunk($reviews, $perPage)[$p - 1];

    foreach ($reviews as $review): ?>
        <div class="border p-3 my-3">
            <?= $review['name'] ?>, <?= date('d.m.Y H:i', $review['time']) ?>
            <div>
                <?= $review['message'] ?>
            </div>
        </div>
  <?php endforeach; ?>

  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i == $p ? 'active' : '' ?>"><a class="page-link" href="/reviews?p=<?= $i ?>"><?= $i ?></a></li>
      <?php endfor; ?>
    </ul>
  </nav>

