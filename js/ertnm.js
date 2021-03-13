var ABILITY_SHARD = "ability_shard";
var COOLDOWN_SHARD = "cooldown_shard";
var PHASE_SHARD = "phase_shard";
var ERT_COOLDOWN_SHARD = "ert_cooldown_shard";
var ERT_PHASE_SHARD = "ert_phase_shard";
var ERT_COOLDOWN_LIST_SHARD = "ert_cooldown_list_shard";
var ABILITY_COOLDOWN_SHARD = "ability_cooldown_shard"
var TYPE_COOLDOWN = 'cooldown';
var TYPE_PHASE = 'phase';
var shards = {};
var cooldowns = [];

var Step = Backbone.Model.extend({});
var StepCollection = Backbone.Collection.extend({
    model: Step
});

var Cooldown = Backbone.Model.extend({});
var CooldownCollection = Backbone.Collection.extend({
    model: Cooldown
});

var steps = new StepCollection();

steps.on("add", function(step) {
    if (step.get('type') == TYPE_PHASE) {
        drawPhase(step);
    } else if (step.get('type') == TYPE_COOLDOWN) {
        drawCooldown(step);
    }

    updateErtNote();
    updateIndexes();
});

steps.on("remove", function(step) {
    updateErtNote();
    updateIndexes();
});

steps.on("change", function() {
    updateErtNote();
    updateIndexes();
});

steps.on("cooldownChange", function() {
    updateErtNote();
    updateIndexes();
});

$(document).ready(function() {
    loadAbilities();

    $('#add-phase').click(addPhase);
    $('#add-cooldown').click(addCooldown);
});

function addPhase() {
    var phaseNo = steps.where({ type: 'phase' }).length + 1;
    var step = new Step({
        id: uuidv4(),
        color: 'F60000',
        type: TYPE_PHASE,
        description: 'Phase ' + phaseNo,
        time: '00:00'
    });
    steps.push(step);
}

function addCooldown() {
    var step = new Step({
        id: uuidv4(),
        color: 'F60000',
        type: TYPE_COOLDOWN,
        description: '',
        cooldowns: new CooldownCollection(),
        time: '00:00'
    });
    steps.push(step);
    $WowheadPower.refreshLinks();
}

function drawPhase(step) {
    var phase_shard = loadShard(PHASE_SHARD);
    drawStep(phase_shard, step);
}

/**
 * Draw the Step of TYPE_COOLDOWN
 * @param {Step} step
 */
function drawCooldown(step) {
    var ability_shard = loadShard(ABILITY_SHARD);
    var cooldown_shard = loadShard(COOLDOWN_SHARD);
    var ability_cooldown_shard = loadShard(ABILITY_COOLDOWN_SHARD);
    drawStep(cooldown_shard, step);

    $('tr[data-step="' + step.id + '"] > td.droppable-ability').droppable({
        accept: ".draggable-ability",
        drop: function(event, ui) {
            var id = ui.draggable.data('ability-id');
            var name = ui.draggable.data('ability-name');

            var cooldown = new Cooldown({
                id: uuidv4(),
                abilityId: id,
                abilityName: name,
                description: ''
            });

            step.get('cooldowns').push(cooldown);
            step.trigger("cooldownChange");

            $(this).append(Mustache.render(ability_cooldown_shard, { cooldown: cooldown.toJSON() }));
            $(this).find('input[data-id="' + cooldown.get('id') + '"]').on('change', function() {
                step.get('cooldowns').findWhere({ id: cooldown.get('id') }).set('description', $(this).val());
                steps.trigger("cooldownChange");
            });
            $('div[data-id="'+ cooldown.get('id') +'"] i.remove-cooldown').click(function() {
                $('div[data-id="'+ cooldown.get('id') +'"]').remove();
                step.get('cooldowns').remove(cooldown);
                steps.trigger("cooldownChange");
            });
        }
    });

    $('tr[data-step="' + step.id + '"] > td.boss-cooldown > input').on('change', function() {
        step.set('description', $(this).val());
    });
}

/**
 * Draw a Step in the table
 * @param string shard
 * @param {Step} step
 */
function drawStep(shard, step) {
    var json = step.toJSON();
    $('#cooldown-table tbody').append(Mustache.render(shard, json));
    $('#cooldown-table tbody tr[data-step="' + step.id + '"] td input[name=time]').change(function() {
        step.set('time', $(this).val());
    });
    $('#cooldown-table tbody tr[data-step="' + step.id + '"] td select').change(function() {
        step.set('color', $(this).val().toLowerCase());
    });
    $('#cooldown-table tbody tr[data-step="' + step.id + '"] td i.close-button').click(function() {
        $('tr[data-step="' + step.id + '"]').remove();
        steps.remove(step);
    });
}

function updateIndexes() {
    var tds = $('td.step-index');
    for (var td in tds) {
        $('td.step-index').eq(td).html((parseFloat(td) + 1).toString());
    }
}

/**
 * Grab the HTML shard
 * @param string shard
 * @param string dataType
 */
function loadShard(shard, dataType = 'html') {
    if (shards[shard] == undefined) {
        $.ajax({
            url: "views/shards/" + shard + ".html",
            dataType: dataType,
            async: false
        }).done(function(data) {
            shards[shard] = data;
        });
    }

    return shards[shard];
}

/**
 * Load all configured cooldowns
 */
function loadAbilities() {
    var ability_shard = loadShard(ABILITY_SHARD);

    for (var className in classes) {
        var wowClass = classes[className];
        var cell = wowClass.cell;

        wowClass.abilities.forEach(function(ability) {
            $('.' + cell + '-abilities').append(Mustache.render(ability_shard, ability));
        });
    }

    $('div.draggable-ability').draggable({
        helper: 'clone'
    });
}

function updateErtNote() {
    $('#ert_string').html('');

    steps.forEach(function(step) {
        $('#ert_string').html($('#ert_string').html() + formatStep(step) + "\n");
    });
}

function formatStep(step) {
    var ert_cooldown_shard      = loadShard(ERT_COOLDOWN_SHARD, 'text');
    var ert_phase_shard         = loadShard(ERT_PHASE_SHARD, 'text');
    var ert_cooldown_list_shard = loadShard(ERT_COOLDOWN_LIST_SHARD, 'text');

    if (step.get('type') == TYPE_PHASE) {
        return Mustache.render(ert_phase_shard, step.toJSON())
    } else if (step.get('type') == TYPE_COOLDOWN) {
        var text = Mustache.render(ert_cooldown_shard, step.toJSON());

        step.get('cooldowns').forEach(function(cooldown) {
            text = text + Mustache.render(ert_cooldown_list_shard, cooldown.toJSON());
        });

        return text;
    }
}

/**
 * Generate a UUIDv4 UUID
 * https://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid
 */
function uuidv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
      var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
      return v.toString(16);
    });
  }
