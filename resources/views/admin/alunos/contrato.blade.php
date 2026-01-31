<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Contrato de Prestação de Serviços Educacionais - {{ $aluno->nome_completo }}</title>
    <style>
        /* CONFIGURAÇÃO DE MARGEM GLOBAL */
        @page {
            /* Define a margem de texto para TODAS as páginas igualmente */
            margin: 3.5cm 2cm 2.5cm 2cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ELEMENTOS DECORATIVOS */
        .bg-shape {
            position: fixed;
            z-index: -1;
        }

        /* Ajustamos as posições negativas para compensar a margem do @page */
        .shape-top-left {
            top: -4cm;
            left: -2cm;
            width: 180px;
        }

        .shape-top-right {
            top: -3.5cm;
            right: -2cm;
            width: 165px;
        }

        .shape-bottom-left {
            bottom: -2.7cm;
            left: -2cm;
            width: 205px;
        }

        .shape-bottom-right {
            bottom: -2.5cm;
            right: -2.7cm;
            width: 110px;
        }

        /* CONTEÚDO */
        .header {
            text-align: center;
            /* Removido o padding excessivo, agora controlado pelo @page */
            margin-bottom: 20px;
            /* Força o cabeçalho a subir na primeira página */
            margin-top: -1cm; 
        }

        .header img {
            height: 100px; /* Reduzi um pouco para ganhar espaço */
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            border-bottom: 1.5px solid #000;
            padding-bottom: 8px;
        }

        p {
            margin-bottom: 10px;
            text-align: justify;
        }

        .parties {
            margin: 15px 0;
            background-color: #f9f9f9;
            padding: 12px;
            border: 1px solid #eee;
        }

        .clause-title {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 15px 0 5px 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .clause {
            text-align: justify;
        }

        .indent {
            margin-left: 30px;
            margin-top: 5px;
        }

        .values-box {
            margin: 10px auto;
            padding: 10px 20px;
            border: 1px solid #000;
            background-color: #fff;
            font-weight: bold;
            width: 80%;
        }

        /* ASSINATURAS */
        .signature-area {
            margin-top: 30px;
            text-align: center;
            page-break-inside: avoid;
        }

        .signature-container {
            width: 100%;
            margin-top: 20px;
        }

        .signature-block {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 35px auto 5px auto;
        }

        /* RODAPÉ */
        .footer {
            position: fixed;
            bottom: -1.5cm; /* Posicionado dentro da margem inferior */
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8.5pt;
            color: #444;
        }
        /* PULAR PAGINA */
        .page-break { 
            page-break-before: always;
        }

        strong { color: #000; }
    </style>
</head>

<body>

<img src="{{ public_path('images/decor-folhas.png') }}" class="bg-shape shape-top-left">
<img src="{{ public_path('images/decor-circulos-topo.png') }}" class="bg-shape shape-top-right">
<img src="{{ public_path('images/decor-circulo-base.png') }}" class="bg-shape shape-bottom-left">
<img src="{{ public_path('images/decor-borboletas.png') }}" class="bg-shape shape-bottom-right">


    <div class="header">
        <!-- <img src="{{ storage_path('app/public/logo-ninho.png') }}" alt="Logo Ninho" onerror="this.style.display='none'"> -->
        <h1>CONTRATO DE PRESTAÇÃO DE SERVIÇOS EDUCACIONAIS</h1>
    </div>

    <p>
        Pelo presente instrumento particular de Contrato de Prestação de Serviços de Reforço escolar, as partes devidamente identificadas também na <strong>Ficha de Matrícula</strong>, da qual o presente documento é parte integrante na forma de <strong>Anexo I</strong>, resolvem celebrar o presente Contrato, conforme cláusulas e condições a seguir expostas.
    </p>

    <div class="parties">
        <p><strong>CONTRATADO:</strong> NINHO – CENTRO DE DESENVOLVIMENTO INFANTOJUVENIL, inscrita no CNPJ sob o número 59.175.247/0001-91, estabelecido à RUA 13 DE MAIO, Nº 434, BAIRRO CENTRO, NO MUNICÍPIO DE GUANAMBI/BA.</p>
        <p><strong>CONTRATANTE:</strong> {{ $aluno->nome_responsavel ?: $aluno->nome_completo }}, inscrito no {{ $aluno->cpf_responsavel ?? $aluno->cpf ?? 'Não informado' }}, estabelecido à {{ $aluno->endereco }}, responsável pelo aluno <strong>{{ $aluno->nome_completo }}</strong>.</p>
    </div>

    <div class="clause-title">Cláusula 1 - OBJETO</div>
    <div class="clause">
        Constitui objeto do presente Contrato a prestação de serviços de <strong>REFORÇO ESCOLAR</strong>, pelo CONTRATADO, que se obriga a ministrar a instrução, por meio de aulas e demais atividades, de acordo com suas características e peculiaridades. As aulas do reforço escolar terão duração conforme previsto na Ficha de Matrícula e serão ministradas nos dias e horários identificados na referida ficha.<br><br>
        <strong>Início:</strong> {{ \Carbon\Carbon::parse($aluno->data_matricula)->format('d/m/Y') }}<br>
        <strong>Término:</strong> {{ $aluno->termino_contrato ? \Carbon\Carbon::parse($aluno->termino_contrato)->format('d/m/Y') : '07/12/' . \Carbon\Carbon::parse($aluno->data_matricula)->year }}<br>
        <strong>Período:</strong> {{ $aluno->qtd_parcelas }} meses
    </div>

    <div class="clause-title">Cláusula 2 – MATRÍCULA</div>
    <div class="clause">
        2.1 O aluno poderá se matricular no reforço escolar mencionado na Cláusula 1, desde que o CONTRATANTE assine a adesão ao presente Contrato e apresente o <strong>comprovante de pagamento referente a matrícula</strong>.<br><br>
        2.3 Caso o aluno inicie as aulas sem entregar este Contrato assinado pelo responsável, o CONTRATADO assumirá que o CONTRATANTE <strong>conhece e está de acordo com todas as cláusulas</strong> deste Contrato.
    </div>

    <div class="clause-title">Cláusula 3 - VALOR DO REFORÇO ESCOLAR e VENCIMENTO DAS PARCELAS</div>
    <div class="clause">
        3.1 - Como contraprestação pelos serviços educacionais contratados, o CONTRATANTE deverá pagar ao CONTRATADO o valor de:
        <div class="values-box">
            Valor Total: R$ {{ number_format($aluno->valor_total, 2, ',', '.') }}<br>
            Nº de Parcelas: {{ $aluno->qtd_parcelas }}<br>
            Valor da Parcela: R$ {{ number_format($aluno->valor_parcela, 2, ',', '.') }}
        </div>
        3.2 - O pagamento da <strong>primeira parcela deverá ser realizado no ato de matrícula</strong>, necessária para sua efetivação.<br><br>
        3.3 - A Matrícula e as Parcelas devem ser pagas por meio de <strong>cartão de débito/crédito, pix ou espécie</strong>. Após a escolha do meio de pagamento, serão enviados os dados necessários para a efetuação do pagamento para o contratante por meio do whatsapp e/ou e-mail eletrônico.<br><br>
        3.3.1 - O pagamento realizado via maquininha implica ao CONTRATANTE o valor de <strong>todas as tarifas que envolvem a transação</strong>.<br><br>
        3.4 - Em caso de atraso no pagamento, o valor da parcela será acrescido de <strong>multa de 2% e mora diária de 0,33%</strong>.<br><br>
        3.5 - <strong>Planos de Pagamento com Vencimentos Prorrogados:</strong> A anuidade do reforço escolar é integralmente devida. Caso, por solicitação do CONTRATANTE, o vencimento de uma ou mais parcelas seja prorrogado para data posterior ao término do reforço escolar, as obrigações financeiras se estenderão até a data do vencimento da última parcela, independentemente de o término do reforço escolar ter ocorrido em data anterior, ficando o CONTRATANTE obrigado ao pagamento das parcelas ainda que tenham vencimento posterior ao encerramento do Curso.
    </div>

    <!-- <div class="page-break"></div> -->

    <div class="clause-title">CLÁUSULA 4 - DOS SERVIÇOS NÃO COBERTOS</div>
    <div class="clause">
        4.1 - Os valores contratados compreendem exclusivamente a prestação de serviços decorrentes da carga horária constante na Ficha de Matrícula, excluindo, para todos os fins de direito, atividades complementares, tais como:
        <div class="indent">
            I – Colônia de férias;<br>
            II – Demais atividades complementares.
        </div><br>
        4.3 - Constitui, ainda, obrigação do CONTRATANTE o <strong>ressarcimento de eventuais danos materiais</strong> que o ALUNO, culposa ou dolosamente, causar a terceiros ou ao CONTRATADO.
    </div>

    <div class="clause-title">Cláusula 5 - INADIMPLEMENTO</div>
    <div class="clause">
        5.1 - A falta de pagamento, nos precisos vencimentos, de qualquer parcela referente à prestação de serviços pactuada entre as partes, previstas neste Contrato, acarretará a <strong>automática constituição em mora do Responsável</strong>, ou do Aluno, se maior de 18 anos, nos termos do Artigo 397 do Código Civil, constituindo dívida líquida e certa, cobrável pela medida judicial cabível à espécie, em relação a qualquer dos detentores do poder familiar, além da cobrança das atualizações previstas neste Contrato.<br><br>
        5.2 - O CONTRATADO pode optar, cumulativamente ou não, pela:
        <div class="indent">
            I - <strong>Cobrança extrajudicial</strong>, cabendo ao CONTRATANTE arcar com o pagamento dos encargos resultantes da referida cobrança.<br><br>
            II - <strong>Inclusão do nome do CONTRATANTE</strong>, ou do Aluno maior de 18 anos, no Serviço Central de Proteção ao Crédito (SCPC) ou do SERASA e a realização da Cobrança Judicial, devendo o CONTRATANTE arcar com o pagamento dos <strong>honorários advocatícios da ordem de 20% (vinte por cento)</strong>, além das despesas e custas processuais, caso a inadimplência perdure por mais de 90 (noventa) dias, nos termos da legislação aplicável, notadamente o disposto nos Artigos 475, 476 e 477 do Código Civil (Lei 10.406/2002).
        </div>
    </div>

    <div class="clause-title">Cláusula 6 - USO DE IMAGEM</div>
    <div class="clause">
        O CONTRATADO, livre de quaisquer ônus para com o CONTRATANTE / ALUNO, poderá utilizar seu <strong>nome, sua imagem e sua voz</strong> para fins exclusivos de divulgação do CONTRATADO e de suas atividades, podendo, para tanto, reproduzi-la ou divulgá-la junto à internet e quaisquer outros meios de comunicação, públicos e privados, comprometendo-se a não utilizá-la de maneira contrária à moral ou aos bons costumes.
    </div>

    <div class="clause-title">Cláusula 7 - EXTINÇÃO DO CONTRATO</div>
    <div class="clause">
        6.1 - Este Contrato poderá ser rescindido nas seguintes situações:<br><br>
        <strong>a) Pelo CONTRATADO:</strong>
        <div class="indent">
            I. Por desarmonia entre as partes ou quando constatado que o aluno violou a lei ou as regras do regimento interno, desrespeitando os princípios de comportamento e conduta éticos, morais e disciplinares, oportunidade em que será assegurado ao mesmo o direito à ampla defesa.<br><br>
            II. Por inadimplência;<br><br>
            III. Por constatação, por meio de critérios pedagógicos internos da Coordenação / Direção, de que o aluno não está acompanhando o curso satisfatoriamente.
        </div><br>
        <strong>b) Pelo(a) CONTRATANTE:</strong>
        <div class="indent">
            I. Por <strong>Pedido de Desistência formal</strong> comunicada em Requerimento junto à Secretaria do Curso. Não haverá valor jurídico na comunicação verbal da desistência.<br><br>
            — <strong>Desistência ANTES do início das atividades:</strong> Nesse caso, o CONTRATADO restituirá ao CONTRATANTE os valores pagos, descontando-se uma taxa administrativa no importe de <strong>R$ 100,00 (cem reais)</strong>;<br><br>
            — <strong>Desistência APÓS o início das atividades:</strong> Nesse caso, o CONTRATANTE comunicará expressamente à CONTRATADA com pelo menos <strong>30 (trinta) dias de antecedência</strong> e, ainda, a título de multa, obrigado(s) a satisfazer(em) a prestação vencida e a vincenda do mês subsequente ao exercício do direito.
        </div><br>
        6.2 - O presente contrato vigorará da data de sua assinatura até o dia 30 (trinta) de dezembro de {{ \Carbon\Carbon::parse($aluno->data_matricula)->year }}.
    </div>

    <!-- <div class="page-break"></div> -->

    <div class="clause-title">CLÁUSULA 8 - DA ASSIDUIDADE</div>
    <div class="clause">
        7.1 - As faltas às aulas serão igualmente pagas como aula realizada. Para haver remarcação, o não comparecimento deverá ser informado com antecedência de no <strong>mínimo 24 horas</strong>, caso contrário, será considerado como falta.<br><br>
        7.2 - As remarcações devem ser feitas dentro da mesma semana da aula cancelada, ou, no máximo, dentro do mesmo mês, <strong>não sendo cumulativas para o mês seguinte</strong>.<br><br>
        7.3 - O tempo de duração de cada aula é de <strong>uma hora e meia (01h30)</strong>, ficando o atraso por conta do contratante.<br><br>
        7.4 - Caso o não comparecimento da profissional, a aula será reagendada.
    </div>

    <div class="clause-title">Cláusula 9 - FORO</div>
    <div class="clause">
        Fica eleito o foro da cidade de <strong>Guanambi/BA</strong> para dirimir quaisquer dúvidas porventura vinculadas ao presente instrumento.
    </div>

    <div class="clause-title">CLÁSULA 10 – CONDIÇÕES GERAIS</div>
    <div class="clause">
        9.1 - O CONTRATANTE Declara estar ciente que é <strong>proibido ao aluno a utilização de telefone celular</strong> com ou sem fone de ouvido ou outro aparelho eletrônico, durante as aulas e qualquer outras atividades didático-pedagógicas, ficando a CONTRATADA autorizada a adotar as medidas cabíveis na hipótese de descumprimento desta proibição, ressalvados os casos autorizados pelos professores.<br><br>
        9.2. - O ALUNO E/OU RESPONSÁVEL/CONTRATANTE são obrigados a manter conduta compatível com o ambiente escolar e regras elementares de convivência em sociedade, <strong>SENDO VEDADA A ADOÇÃO DE CONDUTAS QUE ATENTEM CONTRA A HARMONIA DAS RELAÇÕES E BOA CONVIVÊNCIA</strong>, DENTRE AS QUAIS, DE MODO EXEMPLIFICATIVO, DESTACAM-SE AS SEGUINTES CONDUTAS:
        <div class="indent">
            I – Danificar o patrimônio da CONTRATADA;<br>
            II – Adotar, nas dependências da CONTRATADA, comportamento social inadequado, especialmente de natureza ríspida, agressiva e rebelde, que atente contra moral e bons costumes ou contra as normas estabelecidas pela Contratada;<br>
            III – Agredir física e/ou verbalmente colegas, Professores, funcionários, pais e outras pessoas;<br>
            IV – Portar ou fazer uso, nas dependências do reforço ou proximidades, de armas brancas ou de fogo, de recipientes com gás, brinquedos, objetos perfurantes ou cortantes, incandescentes, que imitem armas ou qualquer outro material perturbador da ordem e dos trabalhos escolares que atentem contra a integridade física de si e de outrem.<br>
            V – Utilizar equipamentos eletrônicos emitentes de sons ou imagens, tais como: MP3, MP4, IPOD, IPAD, CELULAR, CÂMERA FOTOGRÁFICA, NOTEBOOK OU OUTROS OBJETOS QUE DISPERSEM A ATENÇÃO E CAUSEM PREJUÍZOS AO PROCESSO ENSINO APRENDIZAGEM.<br>
            VI – Utilizar, influenciar, incitar ou mesmo fazer apologia ao uso de qualquer tipo de substância entorpecente ou que determine dependência química ou psíquica.<br>
            VII – Filmar ou fotografar, dentro do recinto escolar, seja ambiente, colegas, funcionários ou professores, sem prévia autorização, assim como fazer uso das imagens.<br>
            VIII – Ocupar-se com atividades alheias ao processo ensino-aprendizagem, impedir os colegas de participarem das atividades educativas, incitá-los a se ausentar ou acessar sites com conteúdo impróprio.<br>
            XI – Promover no recinto ou realizar em nome da CONTRATADA, sem autorização expressa, coletas, subscrições, campanhas ou atividades culturais, políticas, religiosas ou comerciais, bem como qualquer evento que possa ocasionar desordem em sua estrutura sócio funcional.
        </div><br>
        9.3 - A infração aos deveres impostos nesta cláusula sujeita o autor (ALUNO OU CONTRATANTE) a penalidades que poderão ser aplicadas de acordo com sua gravidade, considerando-se os fatos ocorridos, os valores da contratada, o respeito a seus regulamentos, dentre outros fatores e poderão ensejar a aplicação de advertência, suspensão, expulsão ou mesmo a não renovação da matrícula.<br><br>
        9.4 - Caso a transgressão apresente indícios de ilícito penal, sem prejuízo da penalidade aplicada pela CONTRATADA, o fato será noticiado aos órgãos competentes.<br><br>
        9.5 - Em virtude das proibições constantes nesta Cláusula, a CONTRATADA <strong>não se responsabiliza pela guarda, perda, roubo, furto ou extravio de objetos de valor</strong> (celulares, ipad, tablets, joias, máquinas fotográficas, aparelhos eletrônicos de qualquer natureza, bicicletas, entre outros), papel moeda, documentos ou outros pertences do ALUNO, CONTRATANTE Responsáveis ou de Terceiros, em suas dependências, inclusive em atividades realizadas fora da contratada.
    </div>

    <div class="clause" style="border: 1px solid #000; padding: 10px; margin-top: 15px;">
        O(S) CONTRATANTE(S) É(SÃO) RESPONSÁVEL(EIS), CIVIL E PENALMENTE, PELA VERACIDADE E AUTENTICIDADE DOS DADOS, DECLARAÇÕES, INFORMAÇÕES E DOCUMENTOS QUE FORNECER(EM) E PELAS CONSEQUÊNCIAS QUE DELES ADVIEREM.<br><br>
        Contratante(s) após ter(em) lido, aceito e assinado, juntamente com a Ficha de Matrícula e demais documentos exigidos pela CONTRATADA para efetivação da matrícula.
    </div>

    <div class="signature-area">
    <p>E, por estarem assim justos e contratados, assinam o presente instrumento em 02 (duas) vias de igual teor e forma, para um só e mesmo fim, juntamente com duas testemunhas.</p>
    
<p><strong>Guanambi, BA, {{ \Carbon\Carbon::parse($aluno->data_matricula)->translatedFormat('d \de F \de Y') }}.</strong></p>
    <div class="signature-container">
        <div class="signature-block">
            <div class="signature-line"></div>
            <strong>CONTRATANTE</strong>
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <strong>CONTRATADO</strong>
        </div>
    </div>
</div>

<div class="footer">
    Rua 13 de Maio, 426, Centro, Guanambi, BA, CEP: 46430-000<br>
    77 9 9861-1138 | centroninho@gmail.com
</div>

</body>
</html>