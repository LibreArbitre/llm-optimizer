<?php
// Language detection
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en');
if (isset($_GET['lang'])) {
    setcookie('lang', $lang, time() + (86400 * 365), '/');
}

$translations = [
    'en' => [
        'title' => 'LLM Optimizer',
        'subtitle' => 'Find the best model for your GPU',
        'how_it_works' => 'How it works?',
        'how_it_works_text' => 'Enter your GPU\'s VRAM and constraints, the tool will suggest the best models and configurations.',
        'gpu_preset' => 'GPU Presets (optional)',
        'vram_available' => 'Available VRAM (GB)',
        'vram_helper' => 'Your GPU\'s VRAM memory',
        'context_constraint' => 'Context constraint',
        'context_none' => 'None - Maximize',
        'context_min' => 'minimum',
        'context_helper' => 'Minimum desired context size',
        'priority' => 'Priority',
        'priority_balanced' => 'Balanced',
        'priority_model' => 'Largest model',
        'priority_context' => 'Maximum context',
        'priority_quality' => 'Best quality (less quantization)',
        'priority_helper' => 'What matters most to you',
        'analyze_btn' => '🔍 Analyze possibilities',
        'top_recommendations' => '🏆 Top Recommendations',
        'optimal' => '⭐ OPTIMAL',
        'excellent' => '✓ EXCELLENT',
        'quantization' => 'Quantization',
        'context' => 'Context',
        'vram_used' => 'VRAM used',
        'max_context' => 'Max possible context',
        'other_configs' => '📋 Other viable configurations',
        'usage' => 'Usage',
        'no_results' => '😔 No configuration found',
        'no_results_text' => 'With {vram} GB of VRAM{context}, no model can be loaded.',
        'no_results_tip' => 'Try reducing the context constraint.',
        'and_context' => ' and a minimum context of {context}K',
        'and_more' => '... and {count} more configurations'
    ],
    'fr' => [
        'title' => 'Optimisateur LLM',
        'subtitle' => 'Trouvez le meilleur modèle pour votre GPU',
        'how_it_works' => 'Comment ça marche ?',
        'how_it_works_text' => 'Indiquez la VRAM de votre GPU et vos contraintes, l\'outil vous proposera les meilleurs modèles et configurations possibles.',
        'gpu_preset' => 'GPU préconfigurés (optionnel)',
        'vram_available' => 'VRAM disponible (GB)',
        'vram_helper' => 'Mémoire VRAM de votre GPU',
        'context_constraint' => 'Contrainte de contexte',
        'context_none' => 'Aucune - Maximiser',
        'context_min' => 'minimum',
        'context_helper' => 'Taille de contexte minimale souhaitée',
        'priority' => 'Priorité',
        'priority_balanced' => 'Équilibré',
        'priority_model' => 'Modèle le plus grand',
        'priority_context' => 'Contexte maximum',
        'priority_quality' => 'Meilleure qualité (moins de quantization)',
        'priority_helper' => 'Ce qui compte le plus pour vous',
        'analyze_btn' => '🔍 Analyser les possibilités',
        'top_recommendations' => '🏆 Meilleures recommandations',
        'optimal' => '⭐ OPTIMAL',
        'excellent' => '✓ EXCELLENT',
        'quantization' => 'Quantization',
        'context' => 'Contexte',
        'vram_used' => 'VRAM utilisée',
        'max_context' => 'Contexte max possible',
        'other_configs' => '📋 Autres configurations viables',
        'usage' => 'Usage',
        'no_results' => '😔 Aucune configuration trouvée',
        'no_results_text' => 'Avec {vram} GB de VRAM{context}, aucun modèle ne peut être chargé.',
        'no_results_tip' => 'Essayez de réduire la contrainte de contexte.',
        'and_context' => ' et un contexte minimum de {context}K',
        'and_more' => '... et {count} autres configurations'
    ]
];

$t = $translations[$lang];

function translate($key, $replacements = []) {
    global $t;
    $text = $t[$key] ?? $key;
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    return $text;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= translate('title') ?> - <?= translate('subtitle') ?></title>
    <meta name="description" content="Find the optimal LLM model configuration for your GPU's VRAM. Calculate context size, quantization, and model size trade-offs.">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .lang-selector {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .lang-btn {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            color: #333;
        }
        
        .lang-btn:hover {
            background: white;
            transform: translateY(-2px);
        }
        
        .lang-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            margin-top: 60px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .helper-text {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }
        
        input[type="number"], select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .gpu-presets {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .gpu-preset-btn {
            padding: 12px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }
        
        .gpu-preset-btn:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .gpu-preset-btn.active {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        
        .inline-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button[type="submit"]:hover {
            transform: translateY(-2px);
        }
        
        .results {
            margin-top: 30px;
        }
        
        .results-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .results-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
            border-left: 4px solid #667eea;
            padding-left: 15px;
        }
        
        .model-card {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            transition: all 0.2s;
        }
        
        .model-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        
        .model-card.optimal {
            border-color: #4caf50;
            background: #f1f8f4;
        }
        
        .model-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .model-name {
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }
        
        .model-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-optimal {
            background: #4caf50;
            color: white;
        }
        
        .badge-possible {
            background: #2196f3;
            color: white;
        }
        
        .model-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .inline-group {
                grid-template-columns: 1fr;
            }
            
            .gpu-presets {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .container {
                padding: 25px;
                margin-top: 80px;
            }
            
            .lang-selector {
                top: 10px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="lang-selector">
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">EN</a>
        <a href="?lang=fr" class="lang-btn <?= $lang === 'fr' ? 'active' : '' ?>">FR</a>
    </div>
    
    <div class="container">
        <h1>🎯 <?= translate('title') ?></h1>
        <p class="subtitle"><?= translate('subtitle') ?></p>
        
        <div class="info-box">
            <strong>💡 <?= translate('how_it_works') ?></strong> <?= translate('how_it_works_text') ?>
        </div>
        
        <form method="POST" id="mainForm">
            <input type="hidden" name="lang" value="<?= $lang ?>">
            
            <div class="form-group">
                <label><?= translate('gpu_preset') ?></label>
                <div class="gpu-presets">
                    <button type="button" class="gpu-preset-btn" data-vram="8">RTX 4060<br>8 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="12">RTX 4070<br>12 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="16">RTX 4070 Ti<br>16 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="16">RTX 5070 Ti<br>16 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="24">RTX 4090<br>24 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="24">L4/A10<br>24 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="48">L40S<br>48 GB</button>
                    <button type="button" class="gpu-preset-btn" data-vram="80">H100/A100<br>80 GB</button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="vram"><?= translate('vram_available') ?></label>
                <input type="number" id="vram" name="vram" step="0.1" min="1" 
                       value="<?= $_POST['vram'] ?? '16' ?>" required>
                <div class="helper-text"><?= translate('vram_helper') ?></div>
            </div>
            
            <div class="inline-group">
                <div class="form-group">
                    <label for="context_constraint"><?= translate('context_constraint') ?></label>
                    <select id="context_constraint" name="context_constraint">
                        <option value="0" <?= ($_POST['context_constraint'] ?? '0') == '0' ? 'selected' : '' ?>><?= translate('context_none') ?></option>
                        <option value="4096" <?= ($_POST['context_constraint'] ?? '') == '4096' ? 'selected' : '' ?>>4K <?= translate('context_min') ?></option>
                        <option value="8192" <?= ($_POST['context_constraint'] ?? '') == '8192' ? 'selected' : '' ?>>8K <?= translate('context_min') ?></option>
                        <option value="16384" <?= ($_POST['context_constraint'] ?? '') == '16384' ? 'selected' : '' ?>>16K <?= translate('context_min') ?></option>
                        <option value="32768" <?= ($_POST['context_constraint'] ?? '') == '32768' ? 'selected' : '' ?>>32K <?= translate('context_min') ?></option>
                        <option value="65536" <?= ($_POST['context_constraint'] ?? '') == '65536' ? 'selected' : '' ?>>64K <?= translate('context_min') ?></option>
                        <option value="131072" <?= ($_POST['context_constraint'] ?? '') == '131072' ? 'selected' : '' ?>>128K <?= translate('context_min') ?></option>
                    </select>
                    <div class="helper-text"><?= translate('context_helper') ?></div>
                </div>
                
                <div class="form-group">
                    <label for="priority"><?= translate('priority') ?></label>
                    <select id="priority" name="priority">
                        <option value="balanced" <?= ($_POST['priority'] ?? 'balanced') == 'balanced' ? 'selected' : '' ?>><?= translate('priority_balanced') ?></option>
                        <option value="model_size" <?= ($_POST['priority'] ?? '') == 'model_size' ? 'selected' : '' ?>><?= translate('priority_model') ?></option>
                        <option value="context" <?= ($_POST['priority'] ?? '') == 'context' ? 'selected' : '' ?>><?= translate('priority_context') ?></option>
                        <option value="quality" <?= ($_POST['priority'] ?? '') == 'quality' ? 'selected' : '' ?>><?= translate('priority_quality') ?></option>
                    </select>
                    <div class="helper-text"><?= translate('priority_helper') ?></div>
                </div>
            </div>
            
            <button type="submit"><?= translate('analyze_btn') ?></button>
        </form>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vram_available = floatval($_POST['vram']);
            $context_constraint = intval($_POST['context_constraint']);
            $priority = $_POST['priority'];
            
            // Popular LLM models
            $models = [
                ['name' => 'Llama 3.3 70B', 'params' => 70, 'category' => 'Large'],
                ['name' => 'Llama 3.1 70B', 'params' => 70, 'category' => 'Large'],
                ['name' => 'Mixtral 8x7B', 'params' => 47, 'category' => 'MoE'],
                ['name' => 'Qwen 2.5 32B', 'params' => 32, 'category' => 'Medium'],
                ['name' => 'Qwen 2.5 14B', 'params' => 14, 'category' => 'Medium'],
                ['name' => 'Llama 3.1 8B', 'params' => 8, 'category' => 'Small'],
                ['name' => 'Mistral 7B', 'params' => 7, 'category' => 'Small'],
                ['name' => 'Phi-3 Mini 3.8B', 'params' => 3.8, 'category' => 'Tiny'],
                ['name' => 'Gemma 2 9B', 'params' => 9, 'category' => 'Small'],
                ['name' => 'Gemma 2 2B', 'params' => 2, 'category' => 'Tiny'],
            ];
            
            // Available precisions
            $precisions = [
                'FP16' => 2,
                'FP8' => 1,
                'FP4' => 0.5,
            ];
            
            // Context sizes to test
            $context_sizes = [4096, 8192, 16384, 32768, 65536, 131072, 262144];
            
            $possibilities = [];
            
            foreach ($models as $model) {
                foreach ($precisions as $prec_name => $prec_factor) {
                    $model_memory = $model['params'] * $prec_factor;
                    
                    // 5% safety margin for overhead
                    $usable_vram = $vram_available * 0.95;
                    $vram_for_context = $usable_vram - $model_memory;
                    
                    if ($vram_for_context > 0) {
                        // Calculate maximum possible context
                        $max_context_tokens = floor($vram_for_context / 0.0005);
                        
                        // Find closest standard context
                        $optimal_context = 0;
                        foreach ($context_sizes as $ctx) {
                            if ($ctx <= $max_context_tokens) {
                                $optimal_context = $ctx;
                            }
                        }
                        
                        // Check if it meets the constraint
                        if ($optimal_context >= $context_constraint && $optimal_context > 0) {
                            $context_memory = $optimal_context * 0.0005;
                            $total_vram = $model_memory + $context_memory;
                            
                            // Score for sorting
                            $score = 0;
                            if ($priority === 'model_size') {
                                $score = $model['params'] * 1000 - $prec_factor * 100 + $optimal_context / 1000;
                            } elseif ($priority === 'context') {
                                $score = $optimal_context * 1000 + $model['params'];
                            } elseif ($priority === 'quality') {
                                $score = (3 - $prec_factor) * 10000 + $model['params'] * 100 + $optimal_context / 1000;
                            } else { // balanced
                                $score = $model['params'] * 100 + $optimal_context / 100 - $prec_factor * 50;
                            }
                            
                            $possibilities[] = [
                                'model' => $model,
                                'precision' => $prec_name,
                                'precision_factor' => $prec_factor,
                                'context' => $optimal_context,
                                'model_memory' => $model_memory,
                                'context_memory' => $context_memory,
                                'total_vram' => $total_vram,
                                'vram_usage_percent' => ($total_vram / $vram_available) * 100,
                                'score' => $score,
                                'max_possible_context' => $max_context_tokens
                            ];
                        }
                    }
                }
            }
            
            // Sort by score
            usort($possibilities, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });
            
            echo '<div class="results">';
            
            if (count($possibilities) > 0) {
                // Top 3 recommendations
                echo '<div class="results-section">';
                echo '<h3>' . translate('top_recommendations') . '</h3>';
                
                $top_results = array_slice($possibilities, 0, 3);
                foreach ($top_results as $index => $config) {
                    $badge_class = $index === 0 ? 'badge-optimal' : 'badge-possible';
                    $card_class = $index === 0 ? 'model-card optimal' : 'model-card';
                    $badge_text = $index === 0 ? translate('optimal') : translate('excellent');
                    
                    echo '<div class="' . $card_class . '">';
                    echo '<div class="model-header">';
                    echo '<div class="model-name">' . htmlspecialchars($config['model']['name']) . '</div>';
                    echo '<div class="model-badge ' . $badge_class . '">' . $badge_text . '</div>';
                    echo '</div>';
                    echo '<div class="model-details">';
                    echo '<div class="detail-item">';
                    echo '<div class="detail-label">' . translate('quantization') . '</div>';
                    echo '<div class="detail-value">' . $config['precision'] . '</div>';
                    echo '</div>';
                    echo '<div class="detail-item">';
                    echo '<div class="detail-label">' . translate('context') . '</div>';
                    echo '<div class="detail-value">' . number_format($config['context'] / 1024, 0) . 'K tokens</div>';
                    echo '</div>';
                    echo '<div class="detail-item">';
                    echo '<div class="detail-label">' . translate('vram_used') . '</div>';
                    echo '<div class="detail-value">' . number_format($config['total_vram'], 1) . ' GB (' . number_format($config['vram_usage_percent'], 0) . '%)</div>';
                    echo '</div>';
                    echo '<div class="detail-item">';
                    echo '<div class="detail-label">' . translate('max_context') . '</div>';
                    echo '<div class="detail-value">' . number_format($config['max_possible_context'] / 1024, 0) . 'K tokens</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                
                // All other possibilities
                if (count($possibilities) > 3) {
                    echo '<div class="results-section">';
                    echo '<h3>' . translate('other_configs') . ' (' . (count($possibilities) - 3) . ')</h3>';
                    
                    $other_results = array_slice($possibilities, 3, 10);
                    foreach ($other_results as $config) {
                        echo '<div class="model-card">';
                        echo '<div class="model-header">';
                        echo '<div class="model-name">' . htmlspecialchars($config['model']['name']) . ' - ' . $config['precision'] . '</div>';
                        echo '</div>';
                        echo '<div class="model-details">';
                        echo '<div class="detail-item">';
                        echo '<div class="detail-label">' . translate('context') . '</div>';
                        echo '<div class="detail-value">' . number_format($config['context'] / 1024, 0) . 'K tokens</div>';
                        echo '</div>';
                        echo '<div class="detail-item">';
                        echo '<div class="detail-label">' . translate('vram_used') . '</div>';
                        echo '<div class="detail-value">' . number_format($config['total_vram'], 1) . ' GB</div>';
                        echo '</div>';
                        echo '<div class="detail-item">';
                        echo '<div class="detail-label">' . translate('usage') . '</div>';
                        echo '<div class="detail-value">' . number_format($config['vram_usage_percent'], 0) . '%</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    if (count($possibilities) > 13) {
                        echo '<p style="text-align: center; color: #999; margin-top: 15px;">' . 
                             translate('and_more', ['count' => count($possibilities) - 13]) . '</p>';
                    }
                    
                    echo '</div>';
                }
            } else {
                $context_text = $context_constraint > 0 ? 
                    translate('and_context', ['context' => number_format($context_constraint / 1024, 0)]) : '';
                
                echo '<div class="no-results">';
                echo '<h3>' . translate('no_results') . '</h3>';
                echo '<p>' . translate('no_results_text', ['vram' => $vram_available, 'context' => $context_text]) . '</p>';
                echo '<p style="margin-top: 10px;">' . translate('no_results_tip') . '</p>';
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
    </div>
    
    <script>
        // GPU presets handling
        document.querySelectorAll('.gpu-preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.gpu-preset-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('vram').value = this.dataset.vram;
            });
        });
    </script>
</body>
</html>
