import sys
import pytesseract
from PIL import Image
import pdfplumber
import re
import numpy as np
from fuzzywuzzy import fuzz
import json

# ฟังก์ชันอ่านข้อความจากรูปภาพ (ใช้ OCR)
def extract_text_from_image(image_path):
    try:
        img = Image.open(image_path)
        text = pytesseract.image_to_string(img, lang='tha+eng')  # ใช้ภาษาไทยและอังกฤษ
        return text
    except Exception as e:
        return f"Error reading image: {e}"

# ฟังก์ชันอ่านข้อความจากไฟล์ PDF
def extract_text_from_pdf(pdf_path):
    try:
        text = ""
        with pdfplumber.open(pdf_path) as pdf:
            for page in pdf.pages:
                text += page.extract_text()
        return text
    except Exception as e:
        return f"Error reading PDF: {e}"

# ฟังก์ชันตรวจสอบข้อมูลในสลิป
def verify_slip_data(text, expected_name, expected_amount):
    errors = []

    # ทำให้ชื่อที่คาดหวังและข้อความในสลิปสะอาดขึ้น
    expected_name_clean = expected_name.replace(" ", "").lower()
    slip_text_clean = text.replace(" ", "").lower()

    # ใช้ fuzzywuzzy ในการเปรียบเทียบความคล้ายกัน
    name_match = fuzz.partial_ratio(expected_name_clean, slip_text_clean)

    # ถ้าคะแนนการจับคู่สูงพอสมควร (เช่น 80% หรือสูงกว่า)
    if name_match < 80:
        errors.append(f"ชื่อบัญชีผู้รับไม่ตรงกัน: ไม่พบ '{expected_name}' ในสลิป")

    # ตรวจสอบจำนวนเงิน
    amount_pattern = r"\d{1,3}(?:,\d{3})*(?:\.\d{2})?"  # รูปแบบจำนวนเงิน (เช่น 1,500.00)
    amounts_found = re.findall(amount_pattern, text)

    if not amounts_found:
        errors.append("ไม่พบจำนวนเงินในสลิป")
    else:
        try:
            # แปลงค่าทุกจำนวนที่พบให้เป็น float
            amounts_found = [float(amount.replace(",", "")) for amount in amounts_found]
            
            # หาจำนวนที่ใกล้เคียงที่สุดจากที่พบในสลิป
            closest_amount = min(amounts_found, key=lambda x: abs(x - expected_amount))

            if not np.isclose(closest_amount, expected_amount, atol=0.001):  # ใช้ tolerance สำหรับ float
                errors.append(f"จำนวนเงินไม่ตรงกัน: พบ {closest_amount:.2f}, ต้องการ {expected_amount:.2f}")
        except:
            errors.append("รูปแบบจำนวนเงินไม่ถูกต้อง")

    # ส่งผลลัพธ์การตรวจสอบ
    if not errors:
        return {"status": "success", "message": "การโอนเงินถูกต้อง"}
    else:
        return {"status": "error", "message": f"พบข้อผิดพลาด: {', '.join(errors)}"}

# ตัวอย่างการใช้งาน
if __name__ == "__main__":
    # ข้อมูลที่คาดหวัง
    expected_receiver_name = sys.argv[2]
    expected_amount = float(sys.argv[3])

    # อ่านข้อความจากสลิป (เลือกรูปภาพหรือ PDF)
    slip_text = extract_text_from_image(sys.argv[1])  # หรือใช้ extract_text_from_pdf("slip.pdf")

    # ตรวจสอบข้อมูล
    if "Error" not in slip_text:
        result = verify_slip_data(slip_text, expected_receiver_name, expected_amount)
        print(json.dumps(result))  # ส่งผลลัพธ์เป็น JSON
    else:
        print(json.dumps({"status": "error", "message": slip_text}))
