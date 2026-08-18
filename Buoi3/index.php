<?php
// ==========================
// KHỞI TẠO DỮ LIỆU
// ==========================

$name = "";
$email = "";
$subject = "";
$content = "";

$errors = [];

$success = "";


// ==========================
// XỬ LÝ FORM
// ==========================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --------------------------------
    // 1. Lấy và chuẩn hóa dữ liệu
    // --------------------------------

    // Họ tên: bỏ khoảng trắng đầu/cuối
    // và gom nhiều khoảng trắng thành 1
    $name = trim($_POST["name"] ?? "");
    $name = preg_replace('/\s+/', ' ', $name);

    // Email: bỏ khoảng trắng + chuyển thành chữ thường
    $email = trim($_POST["email"] ?? "");
    $email = strtolower($email);

    // Chủ đề
    $subject = trim($_POST["subject"] ?? "");

    // Nội dung
    $content = trim($_POST["content"] ?? "");


    // --------------------------------
    // 2. Kiểm tra HỌ TÊN
    // --------------------------------

    if ($name === "") {

        $errors["name"] = "Vui lòng nhập họ tên.";

    } elseif (mb_strlen($name) < 2 || mb_strlen($name) > 50) {

        $errors["name"] = "Họ tên phải từ 2 đến 50 ký tự.";
    }


    // --------------------------------
    // 3. Kiểm tra EMAIL
    // --------------------------------

    if ($email === "") {

        $errors["email"] = "Vui lòng nhập email.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors["email"] = "Email không đúng định dạng.";
    }


    // --------------------------------
    // 4. Kiểm tra CHỦ ĐỀ
    // --------------------------------

    $allowedSubjects = [
        "Hỗ trợ kỹ thuật",
        "Tư vấn",
        "Góp ý",
        "Khác"
    ];

    if ($subject === "") {

        $errors["subject"] = "Vui lòng chọn chủ đề.";

    } elseif (!in_array($subject, $allowedSubjects, true)) {

        $errors["subject"] = "Chủ đề không hợp lệ.";
    }


    // --------------------------------
    // 5. Kiểm tra NỘI DUNG
    // --------------------------------

    if ($content === "") {

        $errors["content"] = "Vui lòng nhập nội dung.";

    } else {

        $contentLength = mb_strlen($content);

        if ($contentLength < 10 || $contentLength > 500) {

            $errors["content"] =
                "Nội dung phải từ 10 đến 500 ký tự.";
        }
    }


    // --------------------------------
    // 6. Kiểm tra ẢNH
    // --------------------------------

    if (
        !isset($_FILES["avatar"]) ||
        $_FILES["avatar"]["error"] === UPLOAD_ERR_NO_FILE
    ) {

        $errors["avatar"] = "Vui lòng chọn ảnh đại diện.";

    } else {

        $avatar = $_FILES["avatar"];


        // Kiểm tra upload có lỗi không
        if ($avatar["error"] !== UPLOAD_ERR_OK) {

            $errors["avatar"] = "Có lỗi xảy ra khi tải ảnh lên.";

        } else {

            // -------------------------
            // Kiểm tra 1: Có phải ảnh?
            // -------------------------

            $imageInfo = getimagesize($avatar["tmp_name"]);

            if ($imageInfo === false) {

                $errors["avatar"] =
                    "File tải lên không phải là hình ảnh.";
            }


            // -------------------------
            // Kiểm tra 2: Định dạng
            // -------------------------

            $allowedExtensions = [
                "jpg",
                "jpeg",
                "png",
                "gif"
            ];

            $extension = strtolower(
                pathinfo($avatar["name"], PATHINFO_EXTENSION)
            );

            if (!in_array($extension, $allowedExtensions, true)) {

                $errors["avatar"] =
                    "Ảnh chỉ được phép là JPG, JPEG, PNG hoặc GIF.";
            }


            // -------------------------
            // Kiểm tra 3: Dung lượng
            // -------------------------

            $maxSize = 2 * 1024 * 1024;

            if ($avatar["size"] > $maxSize) {

                $errors["avatar"] =
                    "Dung lượng ảnh không được vượt quá 2MB.";
            }
        }
    }


    // --------------------------------
    // 7. Nếu không có lỗi
    // --------------------------------

    if (empty($errors)) {

        // Tạo thư mục uploads nếu chưa có
        if (!is_dir("uploads")) {

            mkdir("uploads", 0777, true);
        }


        // Tạo tên file mới
        $newFileName =
            time() . "_" . basename($avatar["name"]);

        $uploadPath = "uploads/" . $newFileName;


        // Lưu ảnh
        if (
            move_uploaded_file(
                $avatar["tmp_name"],
                $uploadPath
            )
        ) {

            $success =
                "Gửi liên hệ thành công!";


            // Xóa dữ liệu sau khi gửi thành công
            $name = "";
            $email = "";
            $subject = "";
            $content = "";

        } else {

            $errors["avatar"] =
                "Không thể lưu ảnh lên hệ thống.";
        }
    }
}


// ==========================
// HÀM CHỐNG XSS
// ==========================

function e($value)
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Form liên hệ</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px;
            font-family: Arial, sans-serif;
            background: #eef5fb;
        }

        .container {
            width: 600px;
            max-width: 100%;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }

        h1 {
            text-align: center;
            color: #1769aa;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .required {
            color: red;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            height: 130px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #1769aa;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-top: 6px;
        }

        .input-error {
            border: 1px solid #dc3545;
        }

        .success {
            padding: 12px;
            margin-bottom: 20px;
            background: #d4edda;
            color: #155724;
            border-radius: 6px;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: #1769aa;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #0d548a;
        }

        .note {
            color: #777;
            font-size: 13px;
            margin-top: 5px;
        }

    </style>

</head>


<body>

<div class="container">

    <h1>Liên hệ</h1>

    <p class="description">
        Vui lòng nhập đầy đủ thông tin bên dưới.
    </p>


    <!-- Thông báo thành công -->

    <?php if ($success !== ""): ?>

        <div class="success">
            <?php echo e($success); ?>
        </div>

    <?php endif; ?>


    <form
        method="POST"
        enctype="multipart/form-data"
    >


        <!-- ==========================
             HỌ TÊN
        =========================== -->

        <div class="form-group">

            <label>
                Họ tên
                <span class="required">*</span>
            </label>

            <input
                type="text"
                name="name"
                value="<?php echo e($name); ?>"
                class="<?php echo isset($errors["name"]) ? "input-error" : ""; ?>"
                placeholder="Nhập họ tên"
            >

            <?php if (isset($errors["name"])): ?>

                <div class="error">
                    <?php echo e($errors["name"]); ?>
                </div>

            <?php endif; ?>

        </div>


        <!-- ==========================
             EMAIL
        =========================== -->

        <div class="form-group">

            <label>
                Email
                <span class="required">*</span>
            </label>

            <input
                type="text"
                name="email"
                value="<?php echo e($email); ?>"
                class="<?php echo isset($errors["email"]) ? "input-error" : ""; ?>"
                placeholder="example@gmail.com"
            >

            <?php if (isset($errors["email"])): ?>

                <div class="error">
                    <?php echo e($errors["email"]); ?>
                </div>

            <?php endif; ?>

        </div>


        <!-- ==========================
             CHỦ ĐỀ
        =========================== -->

        <div class="form-group">

            <label>
                Chủ đề
                <span class="required">*</span>
            </label>

            <select
                name="subject"
                class="<?php echo isset($errors["subject"]) ? "input-error" : ""; ?>"
            >

                <option value="">
                    -- Chọn chủ đề --
                </option>

                <option
                    value="Hỗ trợ kỹ thuật"
                    <?php
                    echo $subject === "Hỗ trợ kỹ thuật"
                        ? "selected"
                        : "";
                    ?>
                >
                    Hỗ trợ kỹ thuật
                </option>

                <option
                    value="Tư vấn"
                    <?php
                    echo $subject === "Tư vấn"
                        ? "selected"
                        : "";
                    ?>
                >
                    Tư vấn
                </option>

                <option
                    value="Góp ý"
                    <?php
                    echo $subject === "Góp ý"
                        ? "selected"
                        : "";
                    ?>
                >
                    Góp ý
                </option>

                <option
                    value="Khác"
                    <?php
                    echo $subject === "Khác"
                        ? "selected"
                        : "";
                    ?>
                >
                    Khác
                </option>

            </select>


            <?php if (isset($errors["subject"])): ?>

                <div class="error">
                    <?php echo e($errors["subject"]); ?>
                </div>

            <?php endif; ?>

        </div>


        <!-- ==========================
             NỘI DUNG
        =========================== -->

        <div class="form-group">

            <label>
                Nội dung
                <span class="required">*</span>
            </label>

            <textarea
                name="content"
                class="<?php echo isset($errors["content"]) ? "input-error" : ""; ?>"
                placeholder="Nhập nội dung liên hệ..."
            ><?php echo e($content); ?></textarea>


            <p class="note">
                Nội dung phải từ 10 đến 500 ký tự.
            </p>


            <?php if (isset($errors["content"])): ?>

                <div class="error">
                    <?php echo e($errors["content"]); ?>
                </div>

            <?php endif; ?>

        </div>


        <!-- ==========================
             ẢNH ĐẠI DIỆN
        =========================== -->

        <div class="form-group">

            <label>
                Ảnh đại diện
                <span class="required">*</span>
            </label>

            <input
                type="file"
                name="avatar"
                accept=".jpg,.jpeg,.png,.gif"
                class="<?php echo isset($errors["avatar"]) ? "input-error" : ""; ?>"
            >

            <p class="note">
                JPG, JPEG, PNG, GIF - tối đa 2MB.
            </p>


            <?php if (isset($errors["avatar"])): ?>

                <div class="error">
                    <?php echo e($errors["avatar"]); ?>
                </div>

            <?php endif; ?>

        </div>


        <!-- ==========================
             NÚT GỬI
        =========================== -->

        <button
            type="submit"
            class="btn"
        >
            Gửi liên hệ
        </button>

    </form>

</div>

</body>

</html>