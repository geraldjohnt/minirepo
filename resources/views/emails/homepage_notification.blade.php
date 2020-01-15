<div>{{ $data['client_name']}} 様</div>
<br>
<div>この度はMee2boxにお問い合わせ頂き誠にありがとうございました。
送信内容は以下の通りです。</div>
<br>
<div>−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−</div>
<table>
    <tr>
        <th>会社o 名 : </th>
        <td>{{ $data['company_name'] }} 様</td>
    </tr>
    <tr>
        <th>お名前 : </th>
        <td>{{ $data['client_name'] }} 様</td>
    </tr>
    <tr>
        <th>フリガナ : </th>
        <td>{{ $data['client_phonetic'] }} 様</td>
    </tr>
    <tr>
        <th>メールアドレス : </th>
        <td>{{ $data['email'] }}</td>
    </tr>
    <tr>
        <th>お電話番号 : </th>
        <td>{{ $data['number'] }}</td>
    </tr>

    
    <tr>
        <th>お問い合わせ内容 : </th>
        <td>{{ $data['inquiry_type'] }}</td>
    </tr>
    <br>
    
</table>
<br>
@if(isset($data['inquiry_txt']))
        {{ $data['inquiry_txt'] }}
@endif

<div>−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−−</div>
<br>
2営業日以内にMee2box担当者よりご連絡を差し上げます。
<br>
Mee2box<br>
https://mee2box.com/