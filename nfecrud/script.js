document.getElementById("reportForm").addEventListener("submit", function(event) {
    var isValid = true;
    var missingFields = [];

    var requiredFields = [
        { id: "subdistrict", name: "ตำบล" },
        { id: "activity_type", name: "รูปแบบกิจกรรม" },
        { id: "activity_name", name: "ชื่อกิจกรรม" },
        { id: "course_hours", name: "จำนวนชั่วโมง" },
        { id: "profession_group", name: "กลุ่มอาชีพ" },
        { id: "budget", name: "งบประมาณ" },
        { id: "start_date", name: "วันที่เริ่มกิจกรรม" },
        { id: "end_date", name: "วันที่สิ้นสุดกิจกรรม" },
        { id: "problem_issues", name: "สภาพปัญหา/อุปสรรค" },
        { id: "suggestions", name: "ข้อเสนอแนะ" }
    ];

    requiredFields.forEach(function (field) {
        var fieldElement = document.getElementById(field.id);
        if (!fieldElement.value.trim()) {
            isValid = false;
            fieldElement.classList.add("is-invalid");
            missingFields.push(field.name);
        } else {
            fieldElement.classList.remove("is-invalid");
        }
    });

    if (!isValid) {
        event.preventDefault();  // ยกเลิกการส่งฟอร์ม
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกข้อมูลให้ครบทุกช่องที่จำเป็น',
            html: '<ul style="text-align: left;">' + missingFields.map(function(field) {
                return '<li>' + field + '</li>';
            }).join('') + '</ul>',
            position: 'center',
            showConfirmButton: true
        });
    }
});


function previewImage(event, previewId) {
    var file = event.target.files[0];

    if (!file) {
        console.error("ไม่มีไฟล์ถูกเลือกหรือ input ของไฟล์ว่างเปล่า.");
        return;
    }

    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById(previewId);
        output.src = reader.result;
        output.style.display = "block";
    };
    reader.readAsDataURL(file);
}

function calculateTotalRegistration() {
    var maleRegistration = parseInt(document.getElementById("male_registration").value) || 0;
    var femaleRegistration = parseInt(document.getElementById("female_registration").value) || 0;
    var totalRegistration = maleRegistration + femaleRegistration;
    document.getElementById("total_registration").value = totalRegistration;
}

function calculateTotals() {
    var maleRegistration = parseInt(document.getElementById("male_registration").value) || 0;
    var femaleRegistration = parseInt(document.getElementById("female_registration").value) || 0;

    var totalMale = 0;
    totalMale += parseInt(document.getElementById("male_under_15").value) || 0;
    totalMale += parseInt(document.getElementById("male_15_29").value) || 0;
    totalMale += parseInt(document.getElementById("male_30_39").value) || 0;
    totalMale += parseInt(document.getElementById("male_40_49").value) || 0;
    totalMale += parseInt(document.getElementById("male_50_59").value) || 0;
    totalMale += parseInt(document.getElementById("male_60_above").value) || 0;

    if (totalMale > maleRegistration) {
        alert("จำนวนผู้ชายที่ผ่านการฝึกอบรมเกินกว่าจำนวนผู้ลงทะเบียน");
        resetLastInput("male");
    } else {
        document.getElementById("total_male_trainees").value = totalMale;
    }

    var totalFemale = 0;
    totalFemale += parseInt(document.getElementById("female_under_15").value) || 0;
    totalFemale += parseInt(document.getElementById("female_15_29").value) || 0;
    totalFemale += parseInt(document.getElementById("female_30_39").value) || 0;
    totalFemale += parseInt(document.getElementById("female_40_49").value) || 0;
    totalFemale += parseInt(document.getElementById("female_50_59").value) || 0;
    totalFemale += parseInt(document.getElementById("female_60_above").value) || 0;

    if (totalFemale > femaleRegistration) {
        alert("จำนวนผู้หญิงที่ผ่านการฝึกอบรมเกินกว่าจำนวนผู้ลงทะเบียน");
        resetLastInput("female");
    } else {
        document.getElementById("total_female_trainees").value = totalFemale;
    }

    var totalTrainees = totalMale + totalFemale;
    document.getElementById("total_trainees").value = totalTrainees;
}

function calculateTotalKnowledge() {
    var totalMaleTrainees = parseInt(document.getElementById("total_male_trainees").value) || 0;
    var totalFemaleTrainees = parseInt(document.getElementById("total_female_trainees").value) || 0;

    var totalMaleKnowledge = 0;
    totalMaleKnowledge += parseInt(document.getElementById("male_create_job").value) || 0;
    totalMaleKnowledge += parseInt(document.getElementById("male_increase_income").value) || 0;
    totalMaleKnowledge += parseInt(document.getElementById("male_quality_life").value) || 0;
    totalMaleKnowledge += parseInt(document.getElementById("male_local_wisdom").value) || 0;
    totalMaleKnowledge += parseInt(document.getElementById("male_community_enterprise").value) || 0;
    totalMaleKnowledge += parseInt(document.getElementById("male_value_addition").value) || 0;

    if (totalMaleKnowledge > totalMaleTrainees) {
        alert("จำนวนผู้ชายที่ได้รับความรู้เกินกว่าจำนวนผู้ผ่านการฝึกอบรม");
        resetLastInput("male_knowledge");
    } else {
        document.getElementById("total_male_trainees_knowledge").value = totalMaleKnowledge;
    }

    var totalFemaleKnowledge = 0;
    totalFemaleKnowledge += parseInt(document.getElementById("female_create_job").value) || 0;
    totalFemaleKnowledge += parseInt(document.getElementById("female_increase_income").value) || 0;
    totalFemaleKnowledge += parseInt(document.getElementById("female_quality_life").value) || 0;
    totalFemaleKnowledge += parseInt(document.getElementById("female_local_wisdom").value) || 0;
    totalFemaleKnowledge += parseInt(document.getElementById("female_community_enterprise").value) || 0;
    totalFemaleKnowledge += parseInt(document.getElementById("female_value_addition").value) || 0;

    if (totalFemaleKnowledge > totalFemaleTrainees) {
        alert("จำนวนผู้หญิงที่ได้รับความรู้เกินกว่าจำนวนผู้ผ่านการฝึกอบรม");
        resetLastInput("female_knowledge");
    } else {
        document.getElementById("total_female_trainees_knowledge").value = totalFemaleKnowledge;
    }

    var totalTraineesKnowledge = totalMaleKnowledge + totalFemaleKnowledge;
    document.getElementById("total_trainees_knowledge").value = totalTraineesKnowledge;
}

function calculateIncomeTotals() {
    var totalMaleTrainees = parseInt(document.getElementById("total_male_trainees").value) || 0;
    var totalFemaleTrainees = parseInt(document.getElementById("total_female_trainees").value) || 0;

    var totalMale = 0;
    totalMale += parseInt(document.getElementById("male_income_below_3000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_3001_5000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_5001_10000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_10001_15000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_15001_30000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_30001_50000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_50001_100000").value) || 0;
    totalMale += parseInt(document.getElementById("male_income_above_100000").value) || 0;

    if (totalMale > totalMaleTrainees) {
        alert("จำนวนรายได้ที่เพิ่มขึ้นสำหรับผู้ชายเกินกว่าจำนวนผู้ชายที่ผ่านการฝึกอบรม");
        resetLastInput("male_income");
    } else {
        document.getElementById("total_male_income").value = totalMale;
    }

    var totalFemale = 0;
    totalFemale += parseInt(document.getElementById("female_income_below_3000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_3001_5000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_5001_10000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_10001_15000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_15001_30000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_30001_50000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_50001_100000").value) || 0;
    totalFemale += parseInt(document.getElementById("female_income_above_100000").value) || 0;

    if (totalFemale > totalFemaleTrainees) {
        alert("จำนวนรายได้ที่เพิ่มขึ้นสำหรับผู้หญิงเกินกว่าจำนวนผู้หญิงที่ผ่านการฝึกอบรม");
        resetLastInput("female_income");
    } else {
        document.getElementById("total_female_income").value = totalFemale;
    }

    var total = totalMale + totalFemale;
    document.getElementById("total_income").value = total;
}

function resetLastInput(type) {
    if (type === "male" && document.activeElement.id.includes("male")) {
        document.activeElement.value = "";
    } else if (type === "female" && document.activeElement.id.includes("female")) {
        document.activeElement.value = "";
    } else if (type === "male_knowledge" && document.activeElement.id.includes("male")) {
        document.activeElement.value = "";
    } else if (type === "female_knowledge" && document.activeElement.id.includes("female")) {
        document.activeElement.value = "";
    } else if (type === "male_income" && document.activeElement.id.includes("male")) {
        document.activeElement.value = "";
    } else if (type === "female_income" && document.activeElement.id.includes("female")) {
        document.activeElement.value = "";
    }
}

// เรียกใช้ฟังก์ชันนี้ใน oninput ของแต่ละช่อง
document.querySelectorAll('input[type="number"]').forEach(function(input) {
    input.addEventListener('input', function() {
        calculateTotals();
        calculateTotalKnowledge();
        calculateIncomeTotals();
    });
});
