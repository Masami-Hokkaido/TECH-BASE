<html>
<!--編集フォームの機能-->
<?php
//データベース情報
$dsn = "mysql:dbname=ユーザー名;host=MySQLホスト名;charset=utf8mb4";
$user = "ユーザー名";
$upassword = "パスワード";
?>

<?php
if (!empty($_POST["editnum1"])) {
    $enum1 = $_POST["editnum1"];
    $epass1 = $_POST["editpass1"];

  try{
    //データベースへ接続
    $pdo = new PDO($dsn, $user, $upassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    //編集する投稿の名前，コメントを取得
    $sql = 'SELECT name,comment FROM tb1 where no=:no';
    $editselect = $pdo -> prepare($sql);
    $editselect -> bindParam(':no', $enum1, PDO::PARAM_STR);
    $editselect -> execute();
    $esel= $editselect -> fetchAll();
    $ename= $esel[0][0]; //編集する投稿の名前（[0][0]は多次元配列の要素取得方法）
    $ecmt= $esel[0][1]; //編集する投稿のコメント
  }catch(PDOException $e){
    $error = $e->getMessage();
  }
}
?>


<!--投稿フォーム-->
新規投稿
<form action="" method="post">
<input type="hidden" name="editnum" value="<?php if(!empty($enum1)){ echo $enum1; } ?>">
<input type="hidden" name="editpass" value="<?php if(!empty($enum1)){ echo $epass1; } ?>">
<input type="text" name="name" placeholder="名前" value="<?php if(!empty($enum1)){ echo $ename; } ?>">
<input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($enum1)){ echo $ecmt; } ?>">
<input type="password" name="password" placeholder="パスワード">
<input type="submit" value="送信する">
</form>


<!--消去フォーム-->
投稿の消去
<form action="" method="post">
<input type="text" name="delnum" placeholder="投稿番号">
<input type="password" name="delpass" placeholder="パスワード">
<input type="submit" value="削除"><br>
</form>


<!--編集フォーム-->
投稿の編集
<form action="" method="post">
<input type="text" name="editnum1" placeholder="投稿番号">
<input type="password" name="editpass1" placeholder="パスワード">
<input type="submit" value="編集"><br>
</form>
<hr>
</html>



<!--投稿＆編集機能-->
<?php
if (empty($_POST["editnum"])) { //編集番号未入力

  //新規投稿機能
  if(empty($_POST["name"]) || empty($_POST["comment"])){ //コメント未入力
  }else{ //コメント入力済み

    //投稿内容
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $date=date( "Y年m月d日H:i:s" );
    $password=$_POST["password"];
    echo "送信内容"."<br>";
    echo "名前". ":". $name. "<br>";
    echo "コメント". ":". $comment."<br>"."<br>";

    try{
      //データベースへ接続
      $pdo = new PDO($dsn, $user, $upassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

      //データベース内テーブル入力
      $sql = $pdo -> prepare("INSERT INTO tb1(no, name, comment, date, password) SELECT COALESCE (MAX(no)+1,1), :name, :comment, :date, :password from tb1"); //データ挿入のためのインスタンス化されたPDOStatement
      $sql -> bindParam(':name', $name, PDO::PARAM_STR);  //->インスタンス化されたPDOStatemnetのメソッド(bindParam:バインドするメソッド)にアクセス
      $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);  //PDO::PARAM_STRはデータ型
      $sql -> bindParam(':date', $date, PDO::PARAM_STR);
      $sql -> bindParam(':password', $password, PDO::PARAM_STR);
      $sql -> execute();

      //テーブル表示
      $sql = 'SELECT * FROM tb1'; //*は全てのカラムの意
      $stmt = $pdo->query($sql);
      $results = $stmt->fetchAll(); //fetch関数は，該当するデータを1件のみ配列として返す。fetchAllは全てのデータ
      foreach ($results as $row){
        echo $row["no"];
        echo $row["name"];
        echo $row["comment"];
        echo $row["date"]."<br>";
        echo "<hr>";
      }
    }catch(PDOException $e){
      $error = $e->getMessage();
    }
  }
}else{ //編集番号入力済み
  //編集機能
  $name=$_POST["name"];
  $comment=$_POST["comment"];
  $date=date( "Y年m月d日H:i:s" );
  $password=$_POST["password"];
  $enum = $_POST["editnum"];
  $epass = $_POST["editpass"];

  try{
    //データベースへ接続
    $pdo = new PDO($dsn, $user, $upassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    //編集する投稿のパスワードを取得
    $sql = 'SELECT password FROM tb1 where no=:no';
    $editselect = $pdo -> prepare($sql);
    $editselect -> bindParam(':no', $enum, PDO::PARAM_STR);
    $editselect -> execute();
    $esel= $editselect -> fetchAll();
    $esel= $esel[0][0]; //編集する投稿のパスワード（[0][0]は多次元配列の要素取得方法）

    //編集機能実行
    if ($esel == $epass){
      $sql = $pdo -> prepare("UPDATE tb1 set name=:name, comment=:comment, date=:date, password=:password where no=:no");
      $sql -> bindParam(':no', $enum, PDO::PARAM_INT); 
      $sql -> bindParam(':name', $name, PDO::PARAM_STR);
      $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
      $sql -> bindParam(':date', $date, PDO::PARAM_STR);
      $sql -> bindParam(':password', $password, PDO::PARAM_STR);
      $sql -> execute();
    }

    //テーブル表示
    $sql = 'SELECT * FROM tb1'; //*は全てのカラムの意
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(); //fetch関数は，該当するデータを1件のみ配列として返す。fetchAllは全てのデータ
    foreach ($results as $row){
      echo $row["no"];
      echo $row["name"];
      echo $row["comment"];
      echo $row["date"]."<br>";
      echo "<hr>";
    }
  }catch(PDOException $e){
    $error = $e->getMessage();
  }
}
?>



<!--消去機能-->
<?php
//消去番号入力済み
if (!empty($_POST["delnum"])) {
  $dnum = $_POST["delnum"];
  $dpass = $_POST["delpass"];

  try{
    //データベースへ接続
    $pdo = new PDO($dsn, $user, $upassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

    //消去した投稿のパスワードを取得
    $sql = 'SELECT password FROM tb1 where no=:no';
    $delselect = $pdo -> prepare($sql); //データ挿入のためのインスタンス化されたPDOStatement
    $delselect -> bindParam(':no', $dnum, PDO::PARAM_STR);  //PDO::PARAM_STRはデータ型
    $delselect -> execute();
    $dsel= $delselect -> fetchAll();
    $dsel= $dsel[0][0]; //消去パスワード（[0][0]は多次元配列の要素取得方法）

    //テーブル内データ消去
    if ($dsel == $dpass){
      //消去機能実行
      $sql = 'delete from tb1 where no=:no';
      $delete = $pdo->prepare($sql);
      $delete->bindParam(':no', $dnum, PDO::PARAM_INT);
      $delete->execute();

      //投稿番号修正
      $sql = $pdo -> prepare("UPDATE tb1 set no=no-1 where no>:no"); //sqlを実行する準備。データ更新のためのPDOStatementインスタンスを返す
      $sql -> bindParam(':no', $dnum, PDO::PARAM_INT); 
      $sql -> execute();
    }

    //テーブル表示
    $sql = 'SELECT * FROM tb1'; //*は全てのカラムの意
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(); //fetch関数は，該当するデータを1件のみ配列として返す。fetchAllは全てのデータ
    foreach ($results as $row){
      echo $row["no"];
      echo $row["name"];
      echo $row["comment"];
      echo $row["date"]."<br>";
      echo "<hr>";
    }
  }catch(PDOException $e){
    $error = $e->getMessage();
  }
}
?>
