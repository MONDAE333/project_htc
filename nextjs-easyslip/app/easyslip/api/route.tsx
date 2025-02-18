import { NextRequest } from "next/server";

export async function POST(request: NextRequest) {
    const formData = await request.formData()
    const res = await fetch('https://developer.easyslip.com/api/v1/verify', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer 47d53db9-83b6-4ee8-988c-558ace2282ab'
        },
        body: formData
    });

    const data = await res.json();

    // ตัวอย่างของข้อมูลที่ API ส่งกลับ
    const result = {
        status: data.data.status,
        transRef: data.data.transRef,  // รหัสธุรกรรม
        amount: data.data.amount.amount,      // จำนวนเงิน
        receiver_name: data.data.receiver.account.name.th,  // ชื่อผู้รับ
    };

    return Response.json({ data: result });
}
