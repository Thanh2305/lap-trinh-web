<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thu_vien_mini";

// Kết nối database
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Hàm tự định nghĩa: kiểm tra số điện thoại
function kiemTraSoDienThoai($so_dien_thoai)
{
    return preg_match('/^[0-9]{10,11}$/', $so_dien_thoai);
}

// Xử lý thêm độc giả
if (isset($_POST['them'])) {

    $ho_ten = $_POST['ho_ten'];
    $mssv = $_POST['mssv'];
    $lop = $_POST['lop'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    // Kiểm tra dữ liệu bằng điều kiện
    if (!kiemTraSoDienThoai($so_dien_thoai)) {

        echo "<script>
                alert('Số điện thoại phải có 10 hoặc 11 chữ số!');
              </script>";

    } else {

        $sql = "INSERT INTO doc_gia
                (ho_ten, mssv, lop, so_dien_thoai)
                VALUES
                ('$ho_ten', '$mssv', '$lop', '$so_dien_thoai')";

        if ($conn->query($sql) === TRUE) {

            echo "<script>
                    alert('Thêm độc giả thành công!');
                  </script>";

        } else {

            echo "Lỗi: " . $conn->error;
        }
    }
}

// Xử lý xóa độc giả
if (isset($_GET['xoa'])) {

    $id = $_GET['xoa'];

    $sql = "DELETE FROM doc_gia WHERE id = $id";

    if ($conn->query($sql) === TRUE) {

        echo "<script>
                alert('Xóa độc giả thành công!');
                window.location='doc_gia.php';
              </script>";

    } else {

        echo "Lỗi: " . $conn->error;
    }
}

// Lấy danh sách độc giả
$sql = "SELECT * FROM doc_gia";
$result = $conn->query($sql);

// Tổ chức dữ liệu bằng mảng
$ds_doc_gia = [];

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        $ds_doc_gia[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <title>Quản lý độc giả</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f5f5;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        a {
            color: red;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <h1>QUẢN LÝ ĐỘC GIẢ</h1>

    <!-- Form nhập dữ liệu -->
    <form method="POST">

        <label>Họ tên:</label>
        <input type="text" name="ho_ten" required>

        <label>MSSV:</label>
        <input type="text" name="mssv" required>

        <label>Lớp:</label>
        <input type="text" name="lop" required>

        <label>Số điện thoại:</label>
        <input type="text" name="so_dien_thoai" required>

        <button type="submit" name="them">
            Thêm độc giả
        </button>

    </form>

    <h2 style="text-align:center;">
        DANH SÁCH ĐỘC GIẢ
    </h2>

    <!-- Hiển thị dữ liệu dạng bảng -->
    <table>

        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>MSSV</th>
            <th>Lớp</th>
            <th>Số điện thoại</th>
            <th>Thao tác</th>
        </tr>

        <?php if (count($ds_doc_gia) > 0): ?>

            <!-- Vòng lặp duyệt mảng -->
            <?php foreach ($ds_doc_gia as $row): ?>

                <tr>

                    <td>
                        <?= $row['id'] ?>
                    </td>

                    <td>
                        <?= $row['ho_ten'] ?>
                    </td>

                    <td>
                        <?= $row['mssv'] ?>
                    </td>

                    <td>
                        <?= $row['lop'] ?>
                    </td>

                    <td>
                        <?= $row['so_dien_thoai'] ?>
                    </td>

                    <td>
                        <a
                            href="?xoa=<?= $row['id'] ?>"
                            onclick="return confirm('Bạn có chắc muốn xóa độc giả này không?');">
                            Xóa
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="6">
                    Chưa có dữ liệu độc giả
                </td>
            </tr>

        <?php endif; ?>

    </table>

</body>

</html>
