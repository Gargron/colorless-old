(function() {
    var Snowflakes = function() {
        var Field = document.getElementById('snowfield');
        var field = Field.getContext('2d');
        
            Field.width  = window.innerWidth;
            Field.height = window.innerHeight;

        var Flakes = [];

        var Flake = function(x, y) {
            this.x = x;
            this.y = y;
            this.speed_y = Math.floor(Math.random() * 3);
            if (this.speed_y == 0) this.speed_y = 1;
        };

        var generate_flakes = function(number, max_x, max_y) {
            if (number == null) number = 100;
            if (max_x == null) max_x = Field.width;
            if (max_y == null) max_y = Field.height;

            for (var i = 0; i < number; i++) {
                new_flake(max_x, max_y);
            }
        };

        var new_flake = function(max_x, max_y) {
            Flakes.push(new Flake(Math.floor(Math.random() * max_x), Math.floor(Math.random() * max_y)));
        };

        var update_flakes = function() {
            for (var i = 0, length = Flakes.length; i < length; i++) {
                var flake = Flakes[i];
                //flake.x++;
                flake.y = flake.y + flake.speed_y;
                if (flake.x >= Field.width || flake.y >= Field.height) Flakes.splice(i, 1) && new_flake(Field.width, 10);
            }
        };

        var draw_flakes = function() {
            field.clearRect(0, 0, Field.width, Field.height);
            field.fillStyle = 'rgba(255, 255, 255, 0.5)';

            for (var i = 0, length = Flakes.length; i < length; i++) {
                field.fillRect(Flakes[i].x, Flakes[i].y, 3, 3);
            }
        };

        var animation;

        this.fall = function() {
            var count = 0;
            generate_flakes(300);
            animation = window.setInterval(function() {
                update_flakes();
                draw_flakes();
                count++;
            }, 70);
        };

        this.stop = function() {
            window.clearInterval(animation);
        };

        this.fall();
        return this;
    };

    $(function() {
        $("a.enable-snow").toggle(function() {
            $("#snowfield").css("display", "block");
            if(typeof window.Snowflakes == "undefined") window.Snowflakes = new Snowflakes();
            else window.Snowflakes.fall();
        }, function() {
            $("#snowfield").css("display", "none");
            window.Snowflakes.stop();
        });
    });
}).call(this);