
<h2 style="h2 {color: #2d315f;}">Listado de Empresas Registradas en el Certificado BUPSY</h2>
<h4>Fecha y hora del reporte:  {{ date('d-m-Y H:i:s') }}</h4>
<br>
<table>
    <thead>
    <tr>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">#</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Nombre Comercial</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Dirección</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Razon Social</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">RFC</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Teléfono</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Nombre</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Email</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Experiencia en calidad higiénica</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Tipo registro</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Cuenta con RNT</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Folio RNT</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Alta en inventur</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Giro turístico</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Municipio</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Localidad</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
            border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Consultor</th>
        <th style="th {     font-size: 20px;     font-weight: normal;     padding: 8px;     background:  #2d315f;
                border-top: 4px solid #aabcfe;    border-bottom: 1px solid #fff; color: #ffffff; }">Fecha de registro</th>

    </tr>
    </thead>
    <tbody>
    @foreach($empresas as $empresa)
        <tr>
            <td>{{ $loop->index + 1 }}</td>
            <td>{{ $empresa->nombre_comercial }}</td>
            <td>{{ $empresa->direccion }}</td>
            <td>{{ $empresa->razon_social }}</td>
            <td>{{ $empresa->rfc }}</td>
            <td>{{ $empresa->telefono }}</td>
            <td>{{ $empresa->nombre_participante }} {{ $empresa->apellido_paterno }} {{ $empresa->apellido_materno }}</td>
            <td>{{ $empresa->email }}</td>
            <td>{{ $empresa->experiencia_calidad_higienica }}</td>
            <td>{{ $empresa->tipo_registro }}</td>
            <td>{{ $empresa->rnt }}</td>
            <td>{{ $empresa->folio_rnt }}</td>
            <td>{{ $empresa->alta_inventur }}</td>
            <td>{{ $empresa->giro_turistico }}</td>
            <td>{{ $empresa->municipio }}</td>
            <td>{{ $empresa->localidad }}</td>
            <td>{{ $empresa->empresa_consultora }}</td>
            <td>{{ $empresa->fecha_creacion = date("d-m-Y", strtotime($empresa->fecha_creacion)) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>