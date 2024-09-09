<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .card-footer {
            background: linear-gradient(90deg, #3a6073 0%, #16222a 100%);
            color: #ffffff !important;
            padding: 15px 0;
            font-size: 1rem;
            font-family: 'Prompt', sans-serif;
            text-align: center;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .card-footer::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #00c6ff;
            transition: all 0.3s ease;
        }

        .card-footer:hover::after {
            width: 100%;
            left: 0;
        }

        .card-footer a {
            color: #00c6ff !important;
            text-decoration: none;
            margin-left: 10px;
            font-weight: bold;
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .card-footer a:hover {
            color: #ffffff !important;
            text-shadow: 0 0 10px #00c6ff;
        }

        .card-footer span {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="card-footer text-muted text-center">
        <span>พัฒนาโดย &copy; นายวิทวัส ธานีรัตน์ กลุ่มส่งเสริมการศึกษา สำนักงานส่งการเรียนรู้ประจำจังหวัดนครศรีธรรมราช</span> | ติดต่อ: 086-2693290 | 
        <a href="https://line.me/ti/p/~mygame52" target="_blank">Line ID: mygame52</a>
    </div>
</body>
</html>
