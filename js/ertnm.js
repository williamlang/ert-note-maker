var ABILITY_SHARD = "ability_shard";
var COOLDOWN_SHARD = "cooldown_shard";
var ERT_COOLDOWN_SHARD = "ert_cooldown_shard";
var steps = [];
var shards = {};
var cooldowns = [];

class Cooldown {
    constructor(time, ability, player) {
        this.bossAbility = null;
        this.color = '0000FF';
        this.time = time;
        this.ability = ability;
        this.player = player;
    }

    getTime() {
        return this.time;
    }

    getAbility() {
        return this.ability;
    }

    getPlayer() {
        return this.player;
    }

    setBossAbility(ability) {
        this.bossAbility = ability;
    }

    toObj() {
        return {
            time: this.time,
            ability: this.ability.toObj(),
            player: this.player
        };
    }
}

class Ability {
    constructor(id, name) {
        this.id = id;
        this.name = name;
    }

    getId() {
        return this.id;
    }

    getName() {
        return this.name;
    }

    toObj() {
        return {
            id: this.id,
            name: this.name
        }
    }
}

$(document).ready(function() {
    loadAbilities();

    $('.ability-cd').on('click', function() {
        var cooldown_shard = loadShard(COOLDOWN_SHARD);

        var id = $(this).data('ability-id');
        var name = $(this).data('ability-name');

        var ability = new Ability(id, name);
        var cooldown = new Cooldown('00:00', ability, "");
        cooldowns.push(cooldown);

        updateErtNote();

        $('#cooldown-table > tbody').append(Mustache.render(cooldown_shard, { id: cooldowns.length, cooldown: cooldown.toObj() }));
        $('#step-remove-' + steps.length).on('click', function() {
            updateErtNote();
        });
    });
});

function loadShard(shard, dataType = 'html') {
    if (shards[shard] == undefined) {
        $.ajax({
            url: shard + ".html",
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
            $('.' + cell + '-abilities').append(Mustache.render(ability_shard, ability ));
        });
    }
}

function updateErtNote() {
    $('#ert_string').html('');

    for (var cooldown in cooldowns) {
        var html = $('#ert_string').html();

        $('#ert_string').html(html + "\n" + formatCooldown(cooldowns[cooldown]));
    }
}

function formatCooldown(cooldown) {
    var ert_cooldown_shard = loadShard(ERT_COOLDOWN_SHARD, 'text');
    return Mustache.render(ert_cooldown_shard, cooldown);
}
