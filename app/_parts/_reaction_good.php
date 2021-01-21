            <!-- いいねボタン -->
            <!-- $goodArray(ユーザーがいいねしたログ)の中に、post idが含まれている時(いいね済みの時) -->
            <!-- パーツ化 -->

            <?php $goodFlag = in_array($post['id'], $goodArray, true); ?>
            <?php if ($goodFlag) : ?>
            <div id="like-div">
              <a class="btn btn-danger btn-sm testLikebtn subLikebtn contents-cancelGoodBtn" role="button" data-postid="<?= h($post['id']); ?>" data-memberid="<?= h($member['id']); ?>">
                <i class="good fas fa-heart"></i>
                <span class="ccGB_goodNumberSpace"><?= h($post['good']); ?></span>
              </a>

              <a class="btn btn-outline-danger btn-sm testLikebtn addLikebtn d-none contents-addGoodBtn" role="button" data-postid="<?= h($post['id']); ?>" data-memberid="<?= h($member['id']); ?>">
                <i class="good fas fa-heart"></i>
                <span class="caGB_goodNumberSpace"><?= h($post['good']); ?></span>
              </a>
            </div>

            <?php else: ?>

            <div class="like-div">
              <a class="btn btn-danger btn-sm testLikebtn subLikebtn d-none contents-cancelGoodBtn" role="button" data-postid="<?= h($post['id']); ?>" data-memberid="<?= h($member['id']); ?>">
                <i class="good fas fa-heart"></i>
                <span class="ccGB_goodNumberSpace"><?= h($post['good']); ?></span>
              </a>

              <a class="btn btn-outline-danger btn-sm testLikebtn addLikebtn contents-addGoodBtn" role="button" data-postid="<?= h($post['id']); ?>" data-memberid="<?= h($member['id']); ?>">
                <i class="good fas fa-heart"></i>
                <span class="caGB_goodNumberSpace"><?= h($post['good']); ?></span>
              </a>
            </div>

            <?php endif; ?>

            <?php if ($_SESSION['id'] == $post['member_id']) : ?>
              <a class="btn btn-outline-primary btn-sm trashbtn" role="button" data-postid="<?= h($post['id']); ?>">
                <i class="fas fa-trash"></i>
              </a>
            <?php endif; ?>