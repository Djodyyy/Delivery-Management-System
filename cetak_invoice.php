<?php
require_once __DIR__ . '/vendor/tecnickcom/tcpdf/tcpdf.php';
require_once 'functions/function_pengiriman.php';

if (!isset($_GET['tanggal'])) {
    die("Parameter tanggal diperlukan.");
}

$tanggal = $_GET['tanggal'];
$tanggal_format = date('d-m-Y', strtotime($tanggal));
$no_invoice_utama = "HK-" . date('Ymd', strtotime($tanggal));

// Ambil data pengiriman
$query = $conn->prepare("
    SELECT pg.*, pr.nama_perusahaan, mb.no_polisi, rt.tujuan
    FROM tb_pengiriman pg
    JOIN tb_perusahaan pr ON pg.id_perusahaan = pr.id_perusahaan
    JOIN tb_mobil mb ON pg.id_mobil = mb.id_mobil
    JOIN tb_rute rt ON pg.id_rute = rt.id_rute
    WHERE pg.tanggal = ?
");
$query->bind_param("s", $tanggal);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Tidak ada data pengiriman pada tanggal ini.");
}

// PDF Setup
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('Sistem PT. HKN');
$pdf->SetAuthor('PT. Haikal Karya Nursyaban');
$pdf->SetTitle('Invoice Pengiriman - ' . $no_invoice_utama);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

// Header dengan logo
$logoPath = __DIR__ . '/assets/images/logos/logo.png';
if (file_exists($logoPath)) {
    $pdf->Image($logoPath, 15, 13, 30); // geser ke bawah Y = 13
}

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(50, 15); // Geser lebih bawah agar tidak nempel garis
$pdf->Cell(0, 7, 'PT. HAIKAL KARYA NURSYABAN', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetX(50);
$pdf->MultiCell(0, 5,
    "Hegarmanah RT 017 RW 005 Cisalada, Jatiluhur, Purwakarta - Jawa Barat\n" .
    "Telp: 0877 7992 0005 | Email: haikalkaryanursyaban16@gmail.com", 0, 'L');

// Garis bawah header
$pdf->Line(15, 40, 285, 40);

$pdf->Ln(14);
$pdf->SetFont('helvetica', '', 11);
$pdf->Write(0, "No Invoice: $no_invoice_utama", '', 0, 'L');
$pdf->Ln(6);
$row_first = $result->fetch_assoc();
$customer = $row_first['nama_perusahaan'];
$alamat = 'Subang'; // Atau bisa dari DB kalau ada kolom alamat di tb_perusahaan
$periode = strtoupper(date('F Y', strtotime($tanggal))); // Contoh: JULI 2024
$result->data_seek(0);
$pdf->Write(0, "Customer : $customer", '', 0, 'L');
$pdf->Ln(6);
$pdf->Write(0, "Alamat   : $alamat", '', 0, 'L');
$pdf->Ln(6);
$pdf->Write(0, "Periode  : $periode", '', 0, 'L');
$pdf->Ln(10);

// Tabel
// Tabel
$html = '
<table border="1" cellpadding="4" cellspacing="0">
    <thead>
        <tr style="background-color:#f2f2f2; font-weight:bold; text-align:center;">
            <th width="5%">#</th>
            <th width="22%">No. Invoice</th>
            <th width="23%">Perusahaan</th>
            <th width="10%">Mobil</th>
            <th width="10%">Rute</th>
            <th width="6%">CT</th>
            <th width="6%">Qty</th>
            <th width="9%">Biaya Lain</th>
            <th width="9%">Total</th>
        </tr>
    </thead>
    <tbody>
';

$no = 1;
$grand_total = 0;
while ($row = $result->fetch_assoc()) {
    $html .= '
    <tr>
        <td width="5%" align="center">' . $no++ . '</td>
        <td width="22%" style="white-space:nowrap;">' . $row['no_invoice_pengiriman'] . '</td>
        <td width="23%">' . $row['nama_perusahaan'] . '</td>
        <td width="10%">' . $row['no_polisi'] . '</td>
        <td width="10%">' . $row['tujuan'] . '</td>
        <td width="6%" align="center">' . $row['ct'] . '</td>
        <td width="6%" align="center">' . $row['qty'] . '</td>
        <td width="9%" align="right">Rp ' . number_format($row['biaya_lain'], 0, ',', '.') . '</td>
        <td width="9%" align="right">Rp ' . number_format($row['total'], 0, ',', '.') . '</td>
    </tr>';
    $grand_total += $row['total'];
}

$html .= '
    <tr>
        <td colspan="8" align="right"><strong>Grand Total</strong></td>
        <td align="right"><strong>Rp ' . number_format($grand_total, 0, ',', '.') . '</strong></td>
    </tr>
</tbody>
</table>
';


$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(5);

// Footer
$pdf->SetFont('helvetica', 'I', 9);
$pdf->Cell(0, 10, 'Dicetak oleh Sistem PT. HKN pada ' . date('d-m-Y H:i'), 0, false, 'C');

// Output PDF
$pdf->Output('Invoice_' . $no_invoice_utama . '.pdf', 'I');
