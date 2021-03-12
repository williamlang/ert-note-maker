var ABILITY_SHARD = "ability_shard";
var COOLDOWN_SHARD = "cooldown_shard";
var PHASE_SHARD = "phase_shard";
var ERT_COOLDOWN_SHARD = "ert_cooldown_shard";
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

$(document).ready(function() {
    loadAbilities();

    $('#add-phase').click(addPhase);
    $('#add-cooldown').click(addCooldown);
});

function addPhase() {
    var phaseNo = steps.where({ type: 'phase' }).length + 1;
    var step = new Step({
        type: TYPE_PHASE,
        description: 'Phase ' + phaseNo
    });
    steps.push(step);
    drawPhase(step)
}

function addCooldown() {
    var id = steps.length + 1;
    var step = new Step({
        id: id,
        type: TYPE_COOLDOWN,
        bossAbilityId: '',
        bossAbilityName: '',
        description: '',
        abilityId: '',
        abilityName: ''
    });
    steps.push(step);
    drawCooldown(step);
}

function drawPhase(step) {
    var phase_shard = loadShard(PHASE_SHARD);
    drawStep(phase_shard, step);
}

function drawCooldown(step) {
    var ability_shard = loadShard(ABILITY_SHARD);
    var cooldown_shard = loadShard(COOLDOWN_SHARD);
    var ability_cooldown_shard = loadShard(ABILITY_COOLDOWN_SHARD);
    var index = drawStep(cooldown_shard, step);

    $('tr[data-step="' + index + '"] > td.droppable-ability').droppable({
        accept: ".draggable-ability",
        drop: function(event, ui) {
            // ui.draggable.clone().appendTo($(this));
            var id = ui.draggable.data('ability-id');
            var name = ui.draggable.data('ability-name');

            step.set('abilityId', id);
            step.set('abilityName', name);

            $(this).append(Mustache.render(ability_cooldown_shard, { id: id, name: name }));

            $(this).find('input').on('change', function() {
                step.set('description', $(this).val());
            });
        }
    });

    $('tr[data-step="' + index + '"] > td.boss-cooldown > input').on('change', function() {
        var step = steps.findWhere({ id: index });

        var ability = {
            id: $(this).val(),
            name: 'Boss Ability'
        };

        // update the step with the info we need
        step.bossAbilityId = $(this).val();

        $('tr[data-step="' + index + '"] > td.boss-cooldown > .boss-cooldown-spell-link').html('');
        $('tr[data-step="' + index + '"] > td.boss-cooldown > .boss-cooldown-spell-link')
            .append(Mustache.render(ability_shard, ability));

        $WowheadPower.refreshLinks();
    });
}

function drawStep(shard, step) {
    var json = step.toJSON();
    json.index = steps.length;
    $('#cooldown-table tbody').append(Mustache.render(shard, json));
    return json.index;
}

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
}

function formatCooldown(cooldown) {
    var ert_cooldown_shard = loadShard(ERT_COOLDOWN_SHARD, 'text');
    return Mustache.render(ert_cooldown_shard, cooldown);
}
