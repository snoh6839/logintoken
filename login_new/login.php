<section class="login">
    <?php
    // 로그아웃 처리 부분
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["logout"])) {
        session_unset();
        session_destroy();
        header("Location: /login.php");
        exit;
    }

    // 제출된 폼 데이터를 처리하는 부분
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
                $_SESSION['mem_ID'] = $result["mem_ID"];
                echo "<div class='success'>Welcome, {$_SESSION['mem_ID']}! You have successfully logged in.</div><div class='box'><p>Time is of the essence</p><p>Work smarter, not harder</p><p>Prioritize your tasks</p></div><div class='success'>with your task manager Team NUMBER ONE</div><a href='/login.php?logout=true'>Logout</a>";
            } else {
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
</section>
