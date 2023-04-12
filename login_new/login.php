
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mem_ID = $_POST["mem_ID"];
    $pass_no = $_POST["pass_no"];

    // DB 연결 정보
    $servername = "localhost";
    $db_mem_ID = "root";
    $db_pass_no = "root506";
    $dbname = "todolist";

    try {
        // PDO 객체 생성
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_mem_ID, $db_pass_no);

        // PDO 예외 처리 모드 설정
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 쿼리 실행
        $stmt = $conn->prepare("SELECT * FROM member_inf WHERE mem_ID=:mem_ID AND pass_no=:pass_no");
        $stmt->bindParam(":mem_ID", $mem_ID);
        $stmt->bindParam(":pass_no", $pass_no);
        $stmt->execute();

        // 결과 확인
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // 로그인 성공
            $token = bin2hex(random_bytes(16)); // 16바이트 길이의 무작위 토큰 생성
            setcookie("token", $token, time() + 3600, "/"); // 쿠키에 토큰 저장
            $_SESSION['mem_ID'] = $result["mem_ID"];
            echo "<div class='success'>Welcome, {$_SESSION['mem_ID']}! You have successfully logged in.</div><div class='box'><p>Time is of the essence</p><p>Work smarter, not harder</p><p>Prioritize your tasks</p></div><div class='success'>with your task manager Team NUMBER ONE</div><a href='/logout.php'>Logout</a>";
        } else {
            // 로그인 실패
            $login_error = "Login failed. <br> Invalid ID or password.";
            echo "<div class='error box'>$login_error</div><a href='/login.php'>Return</a>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // 연결 종료
    $conn = null;
} else {
    // 아직 폼이 제출되지 않았음
    echo "
    <form method='post' action='login.php'>
    <label for='mem_ID'>ID:</label>
    <input type='text' id='mem_ID' name='mem_ID' required>
    <br>
    <label for='pass_no'>Password:</label>
    <input type='pass_no' id='pass_no' name='pass_no' required>
    <button type='submit'>Login</button>
    </form>";
}
?>
// 토큰 삭제를 위해 쿠키에 저장된 토큰값을 가져옵니다.
$token = $_COOKIE["token"];

// DB 연결 정보
$servername = "localhost";
$db_mem_ID = "root";
$db_pass_no = "root506";
$dbname = "todolist";

try {
    // PDO 객체 생성
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_mem_ID, $db_pass_no);

    // PDO 예외 처리 모드 설정
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 토큰 삭제 쿼리 실행
    $stmt = $conn->prepare("DELETE FROM tokens WHERE token=:token");
    $stmt->bindParam(":token", $token);
    $stmt->execute();

    // 쿠키에서 토큰 삭제
    unset($_COOKIE["token"]);
    setcookie("token", null, -1, "/");

    // 로그아웃 성공 메시지 출력
    echo "<div class='success'>You have successfully logged out.</div><a href='/login.php'>Return to Login</a>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// 연결 종료
$conn = null;
