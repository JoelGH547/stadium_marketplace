<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 3: อัปโหลดรูปภาพสนาม</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .image-box {
      position: relative;
      display: inline-block;
      margin: 10px;
    }
    .image-box img {
      width: 180px;
      height: 140px;
      object-fit: cover;
      border-radius: 10px;
      border: 2px solid #ddd;
    }
    .remove-btn {
      position: absolute;
      top: -10px;
      right: -10px;
      padding: 2px 7px;
      background: red;
      color: white;
      font-size: 18px;
      border: none;
      border-radius: 50%;
      cursor: pointer;
    }
  </style>
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 800px;">
  <a href="<?= base_url('owner/fields/step2') ?>" class="btn btn-secondary mb-3">
    ⬅ ย้อนกลับ
</a>


  <h3 class="fw-bold mb-3">ขั้นตอนที่ 3: อัปโหลดรูปภาพสนาม</h3>
  <p class="text-muted">เลือกหลายรูป / ลบรูปได้</p>

  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('owner/fields/step3') ?>" 
      enctype="multipart/form-data" 
      onsubmit="return validateImages()">


    <!-- ภายนอก -->
    <h5>รูปภายนอก *</h5>
    <input type="file" id="outsideInput" name="outside_images[]" class="form-control" multiple accept="image/*">
    <div id="outsidePreview" class="d-flex flex-wrap mt-3"></div>

    <!-- ภายใน -->
    <h5 class="mt-4">รูปภายใน *</h5>
    <input type="file" id="insideInput" name="inside_images[]" class="form-control" multiple accept="image/*">
    <div id="insidePreview" class="d-flex flex-wrap mt-3"></div>

    <button type="submit" class="btn btn-primary w-100 mt-4">ถัดไป</button>

  </form>
</div>


<script>
// ========== ใช้ DataTransfer เพื่อจัดการไฟล์ ==========
function setupImageUploader(inputId, previewId) {

    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    let dataTransfer = new DataTransfer();

    input.addEventListener("change", function() {

        // เพิ่มไฟล์ใหม่ลง DataTransfer
        for (let file of input.files) {
            dataTransfer.items.add(file);
        }

        // sync input.files
        input.files = dataTransfer.files;

        // แสดง preview ใหม่ทั้งหมด
        renderPreview();
    });

    function renderPreview() {
        preview.innerHTML = "";

        Array.from(dataTransfer.files).forEach((file, index) => {
            let reader = new FileReader();

            reader.onload = function(e) {
                const box = document.createElement("div");
                box.className = "image-box";

                const img = document.createElement("img");
                img.src = e.target.result;

                const btn = document.createElement("button");
                btn.className = "remove-btn";
                btn.innerHTML = "×";

                btn.onclick = function() {
                    dataTransfer.items.remove(index);
                    input.files = dataTransfer.files;
                    renderPreview();
                };

                box.appendChild(img);
                box.appendChild(btn);
                preview.appendChild(box);
            };

            reader.readAsDataURL(file);
        });
    }
}

// ติดตั้งให้ 2 ส่วน
setupImageUploader("outsideInput", "outsidePreview");
setupImageUploader("insideInput", "insidePreview");
</script>
<script>
function validateImages() {
    const outside = document.getElementById("outsideInput").files.length;
    const inside = document.getElementById("insideInput").files.length;

    if (outside < 1) {
        alert("กรุณาเลือกรูปภายนอกอย่างน้อย 1 รูป");
        return false;
    }

    if (inside < 1) {
        alert("กรุณาเลือกรูปภายในอย่างน้อย 1 รูป");
        return false;
    }

    return true;
}
</script>


</body>
</html>
