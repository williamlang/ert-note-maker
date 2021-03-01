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

    getBossAbility() {
        return this.bossAbility;
    }

    getAbility() {
        return this.ability;
    }

    getPlayer() {
        return this.player;
    }

    setTime(time) {
        this.time = time;
    }

    setColor(color) {
        this.color = color;
    }

    setBossAbility(ability) {
        this.bossAbility = ability;
    }

    setPlayer(playerName) {
        this.player = playerName;
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

        $('#' + cooldowns.length + '_time').on('keyup', function() {
            var cooldownId = $(this).data('step') - 1;
            cooldowns[cooldownId].setTime($(this).val());
            updateErtNote();
        });

        $('#' + cooldowns.length + '_boss_ability').on('keyup', function() {
            var cooldownId = $(this).data('step') - 1;
            cooldowns[cooldownId].setBossAbility(new Ability(null, $(this).val()));
            updateErtNote();
        });

        $('#' + cooldowns.length + '_player').on('keyup', function() {
            var cooldownId = $(this).data('step') - 1;
            cooldowns[cooldownId].setPlayer($(this).val());
            updateErtNote();
        });

        $('#' + cooldowns.length + '_color').on('change', function() {
            var cooldownId = $(this).data('step') - 1;
            cooldowns[cooldownId].setColor($(this).val());
            updateErtNote();
        });

        $('#' + cooldowns.length + '_remove').on('click', function() {
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
        $('#ert_string').html(html + formatCooldown(cooldowns[cooldown]) + "\n");
    }
}

function formatCooldown(cooldown) {
    var ert_cooldown_shard = loadShard(ERT_COOLDOWN_SHARD, 'text');
    return Mustache.render(ert_cooldown_shard, cooldown);
}
