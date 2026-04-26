<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();
        $exists = DB::table('conocia_papers')->where('arxiv_id', '2604.09937')->exists();

        $payload = [
            'arxiv_url' => 'https://arxiv.org/abs/2604.09937',
            'original_title' => 'HealthAdminBench: Evaluating Computer-Use Agents on Healthcare Administration Tasks',
            'original_abstract' => 'Healthcare administration accounts for over $1 trillion in annual spending, making it a promising target for LLM-based computer-use agents (CUAs). While clinical applications of LLMs have received significant attention, no benchmark exists for evaluating CUAs on end-to-end administrative workflows. To address this gap, we introduce HealthAdminBench, a benchmark comprising four realistic GUI environments: an EHR, two payer portals, and a fax system, and 135 expert-defined tasks spanning three administrative task types: Prior Authorization, Appeals and Denials Management, and Durable Medical Equipment (DME) Order Processing. Each task is decomposed into fine-grained, verifiable subtasks, yielding 1,698 evaluation points. We evaluate seven agent configurations under multiple prompting and observation settings and find that, despite strong subtask performance, end-to-end reliability remains low: the best-performing agent achieves only 36.3 percent task success, while another configuration attains the highest subtask success rate at 82.8 percent. These results reveal a substantial gap between current agent capabilities and the demands of real-world administrative workflows.',
            'authors' => $this->json([
                'Suhana Bedi',
                'Ryan Welch',
                'Ethan Steinberg',
                'Michael Wornow',
                'Taeil Matthew Kim',
                'Haroun Ahmed',
                'Peter Sterling',
                'Bravim Purohit',
                'Qurat Akram',
                'Angelic Acosta',
                'Esther Nubla',
                'Pritika Sharma',
                'Michael A. Pfeffer',
                'Sanmi Koyejo',
                'Nigam H. Shah',
            ]),
            'arxiv_published_date' => '2026-04-10',
            'arxiv_category' => 'cs.AI',
            'title' => 'HealthAdminBench: por que los agentes de IA aun fallan en la burocracia de la salud',
            'slug' => 'healthadminbench-agentes-ia-administracion-salud',
            'excerpt' => 'Stanford presenta HealthAdminBench, un benchmark para evaluar agentes de IA en tareas administrativas de salud. Aunque algunos sistemas logran buen rendimiento en subtareas, el mejor agente completo solo resolvio 36,3% de los flujos de punta a punta.',
            'content' => $this->content(),
            'key_contributions' => $this->json([
                'Presenta un benchmark especifico para agentes de IA en administracion de salud.',
                'Construye cuatro entornos realistas: EHR, dos portales de pagadores y un sistema de fax.',
                'Define 135 tareas expertas y 1.698 puntos de evaluacion verificables.',
                'Evalua multiples configuraciones de agentes bajo distintos escenarios.',
                'Muestra una brecha fuerte entre exito en subtareas y exito en tareas completas.',
                'Reorienta la discusion de IA medica hacia flujos operativos reales, no solo diagnostico o preguntas clinicas.',
            ]),
            'practical_implications' => $this->json([
                'Los agentes de IA aun requieren evaluacion rigurosa antes de automatizar procesos sensibles de salud.',
                'La confiabilidad end-to-end importa mas que el rendimiento aislado en subtareas.',
                'Hospitales, aseguradoras y prestadores necesitan trazabilidad, auditoria y supervision humana.',
                'La investigacion ofrece un marco util para pensar automatizacion administrativa en Chile y America Latina.',
            ]),
            'difficulty_level' => 'intermedio',
            'image' => null,
            'featured' => true,
            'status' => 'published',
            'views' => 0,
            'reading_time' => 6,
            'published_at' => $now,
            'updated_at' => $now,
        ];

        if (!$exists) {
            $payload['arxiv_id'] = '2604.09937';
            $payload['created_at'] = $now;
        }

        DB::table('conocia_papers')->updateOrInsert(['arxiv_id' => '2604.09937'], $payload);

        $this->clearCaches();
    }

    public function down(): void
    {
        DB::table('conocia_papers')->where('arxiv_id', '2604.09937')->delete();

        $this->clearCaches();
    }

    private function content(): string
    {
        return <<<'HTML'
<h2>La parte menos visible de la salud tambien necesita IA</h2>
<p>Cuando se habla de inteligencia artificial en medicina, la conversacion suele ir hacia diagnostico, imagenes medicas o prediccion de enfermedades. HealthAdminBench mira otra zona: la administracion de salud, ese conjunto de formularios, autorizaciones, portales, apelaciones y documentos que determina si una atencion avanza o se queda atrapada.</p>
<p>La investigacion, liderada desde Stanford, propone un benchmark para evaluar agentes de IA capaces de usar interfaces de computador en tareas administrativas reales. El objetivo no es medir si un modelo conoce una respuesta, sino si puede completar un flujo operativo completo.</p>

<h2>Fuentes originales</h2>
<ul>
<li><a href="https://arxiv.org/abs/2604.09937" target="_blank" rel="noopener noreferrer">Paper en arXiv</a></li>
<li><a href="https://doi.org/10.48550/arXiv.2604.09937" target="_blank" rel="noopener noreferrer">DOI arXiv</a></li>
<li><a href="https://medicine.stanford.edu/news/stories/2026/04/the-1-trillion-problem-ai-still-cant-yet-solve.html" target="_blank" rel="noopener noreferrer">Nota de Stanford Medicine</a></li>
</ul>

<h2>Que construyeron los investigadores</h2>
<p>HealthAdminBench incluye cuatro entornos simulados pero realistas: un registro electronico de salud, dos portales de aseguradoras y un sistema de fax. Sobre ellos, los autores disenaron 135 tareas expertas relacionadas con autorizaciones previas, apelaciones y denegaciones, y ordenes de equipos medicos duraderos.</p>
<p>Las tareas se dividen en 1.698 puntos de evaluacion, lo que permite ver no solo si el agente llega al final, sino en que paso se equivoca. Esa estructura es importante porque los flujos administrativos no fallan como una pregunta de opcion multiple: fallan por detalles, omisiones, documentos equivocados o pasos en el orden incorrecto.</p>

<h2>El resultado mas importante</h2>
<p>El mejor agente evaluado completo solo el 36,3% de las tareas de punta a punta. Otro sistema alcanzo 82,8% de exito en subtareas, pero aun asi quedo lejos de resolver los procesos completos con confiabilidad.</p>
<p>La conclusion es clara: los agentes actuales pueden parecer competentes en pasos aislados, pero los flujos reales exigen continuidad, memoria operativa, manejo de interfaces, verificacion y precision sostenida.</p>

<h2>Por que esto importa</h2>
<p>Stanford Medicine destaca que la administracion de salud en Estados Unidos supera US$1 trillon anual. Si la IA pudiera reducir parte de esa carga, el impacto seria enorme. Pero HealthAdminBench muestra que automatizar procesos sensibles requiere mucho mas que conectar un modelo a una pantalla.</p>
<p>En salud, un error administrativo puede retrasar una autorizacion, bloquear una cobertura o generar trabajo adicional para equipos clinicos y pacientes. Por eso la confiabilidad no es un detalle tecnico, sino una condicion etica y operativa.</p>

<h2>La lectura para America Latina</h2>
<p>El benchmark esta inspirado en el sistema estadounidense, pero la friccion administrativa tambien existe en Chile y America Latina. Hospitales, aseguradoras, prestadores y pacientes conviven con sistemas fragmentados, documentos repetidos y tramites que consumen tiempo.</p>
<p>La oportunidad es real, pero debe abordarse con evaluaciones situadas, auditoria, trazabilidad y supervision humana. HealthAdminBench ofrece una forma de pensar esa transicion con mas rigor: antes de automatizar, hay que medir si el agente realmente puede cumplir el trabajo completo.</p>
HTML;
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function clearCaches(): void
    {
        foreach ([
            'home_page_data',
            'home_page_data_v2',
            'home_latest_papers',
            'papers_featured',
            'papers_arxiv_cats',
        ] as $key) {
            Cache::forget($key);
        }
    }
};
