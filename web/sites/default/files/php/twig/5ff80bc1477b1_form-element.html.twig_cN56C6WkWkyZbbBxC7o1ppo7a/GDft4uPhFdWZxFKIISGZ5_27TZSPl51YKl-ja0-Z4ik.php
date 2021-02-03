<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/contrib/bootstrap_barrio/templates/form/form-element.html.twig */
class __TwigTemplate_dacd2ace7a4ce31a8e375f70da919d0bedfddf150c9f50f066faa2fab6cae244 extends \Twig\Template
{
    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $tags = ["set" => 49, "if" => 52];
        $filters = ["clean_class" => 83, "escape" => 101, "raw" => 112];
        $functions = ["create_attribute" => 49];

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['clean_class', 'escape', 'raw'],
                ['create_attribute']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // line 47
        echo "
";
        // line 49
        $context["label_attributes"] = ((($context["label_attributes"] ?? null)) ? (($context["label_attributes"] ?? null)) : ($this->env->getExtension('Drupal\Core\Template\TwigExtension')->createAttribute()));
        // line 51
        echo "
";
        // line 52
        if ((($context["type"] ?? null) == "checkbox")) {
            // line 53
            echo "  ";
            if ((($context["customtype"] ?? null) == "custom")) {
                // line 54
                echo "    ";
                $context["wrapperclass"] = "custom-control custom-checkbox";
                // line 55
                echo "    ";
                $context["labelclass"] = "custom-control-label";
                // line 56
                echo "    ";
                $context["inputclass"] = "custom-control-input";
                // line 57
                echo "  ";
            } elseif ((($context["customtype"] ?? null) == "switch")) {
                // line 58
                echo "    ";
                $context["wrapperclass"] = "custom-control custom-switch";
                // line 59
                echo "    ";
                $context["labelclass"] = "custom-control-label";
                // line 60
                echo "    ";
                $context["inputclass"] = "custom-control-input";
                // line 61
                echo "  ";
            } else {
                // line 62
                echo "    ";
                $context["wrapperclass"] = "form-check";
                // line 63
                echo "    ";
                $context["labelclass"] = "form-check-label";
                // line 64
                echo "    ";
                $context["inputclass"] = "form-check-input";
                // line 65
                echo "  ";
            }
        }
        // line 67
        echo "
";
        // line 68
        if ((($context["type"] ?? null) == "radio")) {
            // line 69
            echo "  ";
            if ((($context["customtype"] ?? null) == "custom")) {
                // line 70
                echo "    ";
                $context["wrapperclass"] = "custom-control custom-radio";
                // line 71
                echo "    ";
                $context["labelclass"] = "custom-control-label";
                // line 72
                echo "    ";
                $context["inputclass"] = "custom-control-input";
                // line 73
                echo "  ";
            } else {
                // line 74
                echo "    ";
                $context["wrapperclass"] = "form-check";
                // line 75
                echo "    ";
                $context["labelclass"] = "form-check-label";
                // line 76
                echo "    ";
                $context["inputclass"] = "form-check-input";
                // line 77
                echo "  ";
            }
        }
        // line 79
        echo "
";
        // line 81
        $context["classes"] = [0 => "js-form-item", 1 => ("js-form-type-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(        // line 83
($context["type"] ?? null)))), 2 => ((twig_in_filter(        // line 84
($context["type"] ?? null), [0 => "checkbox", 1 => "radio"])) ? (\Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["type"] ?? null)))) : (("form-type-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["type"] ?? null)))))), 3 => ((twig_in_filter(        // line 85
($context["type"] ?? null), [0 => "checkbox", 1 => "radio"])) ? (($context["wrapperclass"] ?? null)) : ("")), 4 => ("js-form-item-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(        // line 86
($context["name"] ?? null)))), 5 => ("form-item-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(        // line 87
($context["name"] ?? null)))), 6 => ((!twig_in_filter(        // line 88
($context["title_display"] ?? null), [0 => "after", 1 => "before"])) ? ("form-no-label") : ("")), 7 => (((        // line 89
($context["disabled"] ?? null) == "disabled")) ? ("disabled") : ("")), 8 => ((        // line 90
($context["errors"] ?? null)) ? ("has-error") : (""))];
        // line 94
        $context["description_classes"] = [0 => "description", 1 => "text-muted", 2 => (((        // line 97
($context["description_display"] ?? null) == "invisible")) ? ("sr-only") : (""))];
        // line 100
        if (twig_in_filter(($context["type"] ?? null), [0 => "checkbox", 1 => "radio"])) {
            // line 101
            echo "  <div";
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method")), "html", null, true);
            echo ">
    ";
            // line 102
            if ( !twig_test_empty(($context["prefix"] ?? null))) {
                // line 103
                echo "      <span class=\"field-prefix\">";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["prefix"] ?? null)), "html", null, true);
                echo "</span>
    ";
            }
            // line 105
            echo "    ";
            if (((($context["description_display"] ?? null) == "before") && $this->getAttribute(($context["description"] ?? null), "content", []))) {
                // line 106
                echo "      <div";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["description"] ?? null), "attributes", [])), "html", null, true);
                echo ">
        ";
                // line 107
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["description"] ?? null), "content", [])), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 110
            echo "    ";
            if (twig_in_filter(($context["label_display"] ?? null), [0 => "before", 1 => "invisible"])) {
                // line 111
                echo "      <label ";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute(($context["label_attributes"] ?? null), "addClass", [0 => ($context["labelclass"] ?? null)], "method"), "setAttribute", [0 => "for", 1 => $this->getAttribute(($context["input_attributes"] ?? null), "id", [])], "method")), "html", null, true);
                echo ">
        ";
                // line 112
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar($this->sandbox->ensureToStringAllowed(($context["input_title"] ?? null)));
                echo "
      </label>
    ";
            }
            // line 115
            echo "    <input";
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["input_attributes"] ?? null), "addClass", [0 => ($context["inputclass"] ?? null)], "method")), "html", null, true);
            echo ">
    ";
            // line 116
            if ((($context["label_display"] ?? null) == "after")) {
                // line 117
                echo "      <label ";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute(($context["label_attributes"] ?? null), "addClass", [0 => ($context["labelclass"] ?? null)], "method"), "setAttribute", [0 => "for", 1 => $this->getAttribute(($context["input_attributes"] ?? null), "id", [])], "method")), "html", null, true);
                echo ">
        ";
                // line 118
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->renderVar($this->sandbox->ensureToStringAllowed(($context["input_title"] ?? null)));
                echo "
      </label>
    ";
            }
            // line 121
            echo "    ";
            if ( !twig_test_empty(($context["suffix"] ?? null))) {
                // line 122
                echo "      <span class=\"field-suffix\">";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["suffix"] ?? null)), "html", null, true);
                echo "</span>
    ";
            }
            // line 124
            echo "    ";
            if (($context["errors"] ?? null)) {
                // line 125
                echo "      <div class=\"invalid-feedback\">
        ";
                // line 126
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["errors"] ?? null)), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 129
            echo "    ";
            if ((twig_in_filter(($context["description_display"] ?? null), [0 => "after", 1 => "invisible"]) && $this->getAttribute(($context["description"] ?? null), "content", []))) {
                // line 130
                echo "      <small";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute(($context["description"] ?? null), "attributes", []), "addClass", [0 => ($context["description_classes"] ?? null)], "method")), "html", null, true);
                echo ">
        ";
                // line 131
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["description"] ?? null), "content", [])), "html", null, true);
                echo "
      </small>
    ";
            }
            // line 134
            echo "  </div>
";
        } else {
            // line 136
            echo "  <fieldset";
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null), 1 => "form-group"], "method")), "html", null, true);
            echo ">
    ";
            // line 137
            if (twig_in_filter(($context["label_display"] ?? null), [0 => "before", 1 => "invisible"])) {
                // line 138
                echo "      ";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["label"] ?? null)), "html", null, true);
                echo "
    ";
            }
            // line 140
            echo "    ";
            if (( !twig_test_empty(($context["prefix"] ?? null)) ||  !twig_test_empty(($context["suffix"] ?? null)))) {
                // line 141
                echo "      <div class=\"input-group\">
    ";
            }
            // line 143
            echo "    ";
            if ( !twig_test_empty(($context["prefix"] ?? null))) {
                // line 144
                echo "      <div class=\"input-group-prepend\">
        <span class=\"field-prefix input-group-text\">";
                // line 145
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["prefix"] ?? null)), "html", null, true);
                echo "</span>
      </div>
    ";
            }
            // line 148
            echo "    ";
            if (((($context["description_display"] ?? null) == "before") && $this->getAttribute(($context["description"] ?? null), "content", []))) {
                // line 149
                echo "      <div";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["description"] ?? null), "attributes", [])), "html", null, true);
                echo ">
        ";
                // line 150
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["description"] ?? null), "content", [])), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 153
            echo "    ";
            echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["children"] ?? null)), "html", null, true);
            echo "
    ";
            // line 154
            if ( !twig_test_empty(($context["suffix"] ?? null))) {
                // line 155
                echo "      <div class=\"input-group-append\">
        <span class=\"field-suffix input-group-text\">";
                // line 156
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["suffix"] ?? null)), "html", null, true);
                echo "</span>
      </div>
    ";
            }
            // line 159
            echo "    ";
            if (( !twig_test_empty(($context["prefix"] ?? null)) ||  !twig_test_empty(($context["suffix"] ?? null)))) {
                // line 160
                echo "      </div>
    ";
            }
            // line 162
            echo "    ";
            if ((($context["label_display"] ?? null) == "after")) {
                // line 163
                echo "      ";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["label"] ?? null)), "html", null, true);
                echo "
    ";
            }
            // line 165
            echo "    ";
            if (($context["errors"] ?? null)) {
                // line 166
                echo "      <div class=\"invalid-feedback\">
        ";
                // line 167
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["errors"] ?? null)), "html", null, true);
                echo "
      </div>
    ";
            }
            // line 170
            echo "    ";
            if ((twig_in_filter(($context["description_display"] ?? null), [0 => "after", 1 => "invisible"]) && $this->getAttribute(($context["description"] ?? null), "content", []))) {
                // line 171
                echo "      <small";
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute($this->getAttribute(($context["description"] ?? null), "attributes", []), "addClass", [0 => ($context["description_classes"] ?? null)], "method")), "html", null, true);
                echo ">
        ";
                // line 172
                echo $this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($this->getAttribute(($context["description"] ?? null), "content", [])), "html", null, true);
                echo "
      </small>
    ";
            }
            // line 175
            echo "  </fieldset>
";
        }
    }

    public function getTemplateName()
    {
        return "themes/contrib/bootstrap_barrio/templates/form/form-element.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  358 => 175,  352 => 172,  347 => 171,  344 => 170,  338 => 167,  335 => 166,  332 => 165,  326 => 163,  323 => 162,  319 => 160,  316 => 159,  310 => 156,  307 => 155,  305 => 154,  300 => 153,  294 => 150,  289 => 149,  286 => 148,  280 => 145,  277 => 144,  274 => 143,  270 => 141,  267 => 140,  261 => 138,  259 => 137,  254 => 136,  250 => 134,  244 => 131,  239 => 130,  236 => 129,  230 => 126,  227 => 125,  224 => 124,  218 => 122,  215 => 121,  209 => 118,  204 => 117,  202 => 116,  197 => 115,  191 => 112,  186 => 111,  183 => 110,  177 => 107,  172 => 106,  169 => 105,  163 => 103,  161 => 102,  156 => 101,  154 => 100,  152 => 97,  151 => 94,  149 => 90,  148 => 89,  147 => 88,  146 => 87,  145 => 86,  144 => 85,  143 => 84,  142 => 83,  141 => 81,  138 => 79,  134 => 77,  131 => 76,  128 => 75,  125 => 74,  122 => 73,  119 => 72,  116 => 71,  113 => 70,  110 => 69,  108 => 68,  105 => 67,  101 => 65,  98 => 64,  95 => 63,  92 => 62,  89 => 61,  86 => 60,  83 => 59,  80 => 58,  77 => 57,  74 => 56,  71 => 55,  68 => 54,  65 => 53,  63 => 52,  60 => 51,  58 => 49,  55 => 47,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/bootstrap_barrio/templates/form/form-element.html.twig", "E:\\xampp\\htdocs\\module-testing\\web\\themes\\contrib\\bootstrap_barrio\\templates\\form\\form-element.html.twig");
    }
}
