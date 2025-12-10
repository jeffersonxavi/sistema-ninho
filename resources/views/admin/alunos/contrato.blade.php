<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Contrato de Matrícula - {{ $aluno->nome_completo }}</title>

    {{-- Estilos CSS para garantir formatação consistente no PDF --}}
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #1e40af;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 5px;
            font-size: 16pt;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            color: #4b5563;
        }

        .data-item {
            margin-bottom: 5px;
        }

        /* Ajuste para alinhar os rótulos */
        .data-item strong {
            display: inline-block;
            width: 160px;
            color: #1f2937;
            vertical-align: top;
        }

        .legal-text {
            text-align: justify;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .signature-box {
            border-top: 1px dashed #ccc;
            padding-top: 30px;
            margin-top: 50px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 300px;
            margin: 50px auto 5px auto;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>CONTRATO DE PRESTAÇÃO DE SERVIÇOS EDUCACIONAIS</h1>
        {{-- Removido o título repetido --}}
        <p>CONTRATO Nº {{ $aluno->id }}/{{ date('Y') }}</p>
    </div>
<div class="data-item">
        <strong>Realizado Por:</strong> 
        {{ $aluno->cadastradoPor->name ?? 'Sistema' }}
    </div>
    {{-- Fim do novo campo --}}
    {{-- DADOS DO ALUNO --}}
    <div class="section-title">1. Dados do Contratante (Aluno/Responsável)</div>
    <div class="data-item"><strong>Nome Completo:</strong> {{ $aluno->nome_completo }}</div>

    {{-- Formatação básica para RG/CPF/Telefone --}}
    <div class="data-item"><strong>RG:</strong> {{ $aluno->rg ?? 'N/A' }}</div>
    <div class="data-item"><strong>CPF:</strong> {{ $aluno->cpf ?? 'N/A' }}</div>
    <div class="data-item"><strong>Telefone:</strong> {{ $aluno->telefone }}</div>

    <div class="data-item"><strong>Responsável (se menor):</strong> {{ $aluno->nome_responsavel }}</div>
    <div class="data-item"><strong>Endereço:</strong> {{ $aluno->endereco }}</div>


    {{-- DADOS DO CURSO/TURMA --}}
    <div class="section-title">2. Detalhes do Curso e Turma</div>
    <div class="data-item"><strong>Turma:</strong> {{ $aluno->turma->nome ?? 'N/A' }}</div>

    {{-- Professor: Adicionando fallback e garantindo que se o professor sair N/A no DB, a falha está no cadastro da Turma --}}
    <div class="data-item">
        {{-- Acessa a coleção de professores, extrai apenas os nomes e os junta com vírgula --}}
        <strong>Professor(es):</strong>
        {{ $aluno->turma->professores->pluck('nome')->implode(', ') ?? 'A definir' }}
    </div>
    <div class="data-item"><strong>Sala:</strong> {{ $aluno->turma->sala->nome ?? 'N/A' }}</div>

    <div class="data-item"><strong>Período:</strong> {{ $aluno->periodo }}</div>
    <div class="data-item"><strong>Dias da Semana:</strong> {{ $aluno->dias_da_semana }}</div>

    {{-- Horário: Corrigido o formato para H:i --}}
    <div class="data-item"><strong>Horário:</strong> {{ $aluno->horario ? \Carbon\Carbon::parse($aluno->horario)->format('H:i') : 'Não Definido' }}</div>

    {{-- Datas Formatadas --}}
    <div class="data-item"><strong>Data de Matrícula:</strong> {{ \Carbon\Carbon::parse($aluno->data_matricula)->format('d/m/Y') }}</div>
    <div class="data-item"><strong>Término Previsto:</strong> {{ $aluno->termino_contrato ? \Carbon\Carbon::parse($aluno->termino_contrato)->format('d/m/Y') : 'Contrato Aberto' }}</div>

    {{-- DADOS FINANCEIROS --}}
    <div class="section-title">3. Condições Financeiras</div>
    <div class="data-item"><strong>Valor Total do Curso:</strong> R$ {{ number_format($aluno->valor_total, 2, ',', '.') }}</div>
    <div class="data-item"><strong>Valor da Parcela:</strong> R$ {{ number_format($aluno->valor_parcela, 2, ',', '.') }}</div>
    <div class="data-item"><strong>Total de Parcelas:</strong> {{ $aluno->qtd_parcelas }}</div>
    <div class="data-item"><strong>Forma de Pagamento:</strong> {{ $aluno->forma_pagamento }}</div>

    {{-- Melhoria: Adicionar o vencimento da primeira parcela --}}
    @php
    // A primeira parcela é importante para o contrato
    $primeiraParcela = $aluno->pagamentos->sortBy('parcela_numero')->first();
    @endphp
    @if ($primeiraParcela)
    <div class="data-item"><strong>1ª Parcela Vencimento:</strong> {{ \Carbon\Carbon::parse($primeiraParcela->data_vencimento)->format('d/m/Y') }}</div>
    @endif


    {{-- TEXTO LEGAL --}}
    <div class="section-title">4. Termos e Condições (Resumo)</div>

    <div class="legal-text">
        <p>Pelo presente instrumento, a Contratada se compromete a prestar serviços educacionais ao Contratante na
            modalidade e período descritos acima, mediante o pagamento das parcelas estipuladas na Cláusula 3. O
            curso tem duração prevista até a data de <strong>{{ $aluno->termino_contrato ? \Carbon\Carbon::parse($aluno->termino_contrato)->format('d/m/Y') : 'prazo indeterminado' }}</strong>.</p>

        <p style="margin-top: 10px;">O atraso no pagamento de qualquer parcela implicará multa de 2% sobre o valor devido e juros de mora de 1% ao mês (pro-rata die).</p>
        {{-- Adicione aqui cláusulas importantes, como cancelamento, material, etc. --}}
    </div>

    {{-- ASSINATURAS: CORREÇÃO CRÍTICA DA DATA --}}
    <div class="signature-box">
        <p>E por estarem justos e contratados, assinam o presente em duas vias de igual teor.</p>

        {{-- Uso de translatedFormat para garantir a data em Português: --}}
        <p>Guanambi, **{{ \Carbon\Carbon::now()->translatedFormat('d \de F \de Y') }}**.</p>

        <div style="display: flex; justify-content: space-around; margin-top: 60px;">
            <div style="display: inline-block; width: 45%;">
                <div class="signature-line"></div>
                <p style="font-size: 10pt;"><strong>Contratante (Aluno/Responsável)</strong></p>
                <p style="font-size: 10pt;">{{ $aluno->nome_completo }}</p>
            </div>
            <div style="display: inline-block; width: 45%;">
                <div class="signature-line"></div>
                <p style="font-size: 10pt;"><strong>Contratada (Escola/Curso)</strong></p>
                <p style="font-size: 10pt;">Nome da Instituição</p>
            </div>
        </div>
    </div>

</body>

</html>