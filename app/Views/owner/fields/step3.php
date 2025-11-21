<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ขั้นตอนที่ 3: อัปโหลดรูปภาพสนาม</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>

    body {
      background: #f1faf8;
    }

    h3 {
      color: #2a8f7a;
      font-weight: 700;
    }

    .upload-box {
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      border-top: 5px solid #4cb7a5;
    }

    /* ปุ่มย้อนกลับ */
    .btn-back {
      background: #d8f7ef;
      color: #2a8f7a;
      border: 1px solid #bfeee4;
    }
    .btn-back:hover {
      background: #c2f0e5;
      color: #1f6f5f;
    }

    /* ปุ่มถัดไป */
    .btn-primary {
      background: #4cb7a5;
      border: none;
    }
    .btn-primary:hover {
      background: #3aa18e;
    }

    /* กล่อง preview */
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
      border: 2px solid #c7ebe4;
    }

    .remove-btn {
      position: absolute;
      top: -10px;
      right: -10px;
      padding: 2px 7px;
      background: #ff4b4b;
      color: white;
      font-size: 18px;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

  </style>
</head>

<body>

<div class="container mt-5" style="max-width: 800px;">

  <!-- ปุ่มย้อนกลับ -->
  <a href="<?= base_url('owner/fields/step2') ?>" class="btn btn-back mb-3">
    ⬅ ย้อนกลับ
  </a>

  <div class="upload-box">

    <h3 class="fw-bold mb-3">ขั้นตอนที่ 3: อัปโหลดรูปภาพสนาม</h3>
    <p class="text-muted">เลือกรูปหลายใบ / ลบรูปที่ไม่ต้องการได้</p>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form 
        method="post" 
        action="<?= base_url('owner/fields/step3') ?>" 
        enctype="multipart/form-data" 
        onsubmit="return validateImages()">

      <!-- ภายนอก -->
      <h5 class="mt-4 text-success fw-bold">รูปภายนอก *</h5>
      <input type="file" id="outsideInput" name="outside_images[]" class="form-control" multiple accept="image/*">
      <div id="outsidePreview" class="d-flex flex-wrap mt-3"></div>

      <!-- ภายใน -->
      <h5 class="mt-4 text-success fw-bold">รูปภายใน *</h5>
      <input type="file" id="insideInput" name="inside_images[]" class="form-control" multiple accept="image/*">
      <div id="insidePreview" class="d-flex flex-wrap mt-3"></div>

      <button type="submit" class="btn btn-primary w-100 mt-4">ถัดไป</button>

    </form>

  </div>
</div>


<script>
// ========== ระบบอัปโหลด + preview + remove ด้วย DataTransfer ==========
function setupImageUploader(inputId, previewId) {

    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    let dataTransfer = new DataTransfer();

    input.addEventListener("change", function() {

        for (let file of input.files) {
            dataTransfer.items.add(file);
        }

        input.files = dataTransfer.files;
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

setupImageUploader("outsideInput", "outsidePreview");
setupImageUploader("insideInput", "insidePreview");


// ========== Validate ก่อนส่งฟอร์ม ==========
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
