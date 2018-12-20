<?php
global $cfg;

if (!$info['title'])
    $info['title'] = "Reenviar Ticket #".$ticketresult->getNumber();

?>
<h3 class="drag-handle"><?php echo $info['title']; ?></h3>
<b><a class="close" href="#"><i class="icon-remove-circle"></i></a></b>
<div class="clear"></div>
<hr/>
<div><p id="msg_info"><i class="icon-info-sign"></i>&nbsp; Buscar usuarios o añadir uno nuevo.</p></div>
<div id="ticket-status" style="display:block; margin:5px;">
    <form method="post" name="status" id="status" action="#ccandcco/33/reenviar/guardar">
    <input type="hidden" value="<?php echo $ticketresult->getThreadId(); ?>" name="threadId">
    <input type="hidden" value="M" name="role">

    <table style="width: 100%;">
    <tbody id="cc_sec">
            <tr>
                <td width="120">
                    <label><strong><?php echo __('Para'); ?>:</strong></label>
                </td>
                <td>
                    <select id="cc-para-reenviar" name="para" class="contacts" placeholder="Agregar"  multiple style="width: 100%;">
                        
                    </select>
                    <script>
                    var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                  '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

                    $('#cc-para-reenviar').selectize({
                        persist: true,
                        maxItems: 1,
                        plugins: ['remove_button'],
                        valueField: 'id',
                        labelField: 'email',
                        searchField: ['name', 'email','phone'],
                        options: [],
                        load: function(query, callback) {
                            if (!query.length) return callback();
                            $.ajax({
                                url: 'ajax.php/users/local?q=' + encodeURIComponent(query),
                                type: 'GET',
                                // async:false,
                                dataType: 'json',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    console.log(res);
                                    callback(res);
                                }
                            });
                        },
                        render: {
                            item: function(item, escape) {
                                //console.log(item.name + '1');
                                return '<div>' +
                                    (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                                '</div>';

                            },
                            option_create: function(item, escape) {
                                return '<div class="create">Agregar <strong>' + escape(item.input) + '</strong>&hellip;</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;

                            // email@address.com
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);

                            // name <email@address.com>
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);

                            return false;
                        },
                        create: function(input) {
                            
                            if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                                idusernew = 0;
                                $.ajax({
                                    url: 'ajax.php/ccandcco/<?php echo $idTicketAssign; ?>/adduser',
                                    type: 'POST',
                                    async:false,
                                    data: { name: input, email:input },
                                    // dataType: 'json',
                                    error: function() {
                                        console.log('error');
                                    },
                                    success: function(res) {
                                        idusernew = res;
                                        
                                    }
                                }); 
                                return {email: input, id:idusernew}; 
                                
                            }
                            var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
                            if (match) {
                                return {
                                    email : match[2],
                                    name  : $.trim(match[1])
                                };
                            }
                            alert('Invalid email address.');
                            return false;
                        }
                    });
                    </script>
                </td>
            </tr>
            <tr>
                <td width="120">
                    <label><strong><?php echo __('CC'); ?>:</strong></label>
                </td>
                <td>
                    <div style="
                    float:left;
                    border: 1px solid #d0d0d0;
                    padding: 10px 10px;
                    width: 100%;
                    background: #fff;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.1);
                    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.1);
                    -webkit-border-radius: 3px;
                    -moz-border-radius: 3px;
                    border-radius: 3px;">
                    <select id="cc-colaboradores-reenviar" name="cc[]" class="contacts" placeholder="Agregar"  multiple style="width: 90%;">
                        
                    </select>
                    <span style="
                    float: right;
                    top: 0px;
                    right: 0px;
                    margin-right: 10px;
                    margin-top: -27px;cursor:pointer;" id="span-cco-reenviar">CCO</span>
                    </div>
                    <script>
                    var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                  '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

                    $('#cc-colaboradores-reenviar').selectize({
                        persist: true,
                        maxItems: null,
                        plugins: ['remove_button'],
                        valueField: 'id',
                        labelField: 'email',
                        searchField: ['name', 'email','phone'],
                        options: [],
                        load: function(query, callback) {
                            if (!query.length) return callback();
                            $.ajax({
                                url: 'ajax.php/users/local?q=' + encodeURIComponent(query),
                                type: 'GET',
                                // async:false,
                                dataType: 'json',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    console.log(res);
                                    callback(res);
                                }
                            });
                        },
                        render: {
                            item: function(item, escape) {
                                //console.log(item.name + '1');
                                return '<div>' +
                                    (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                                '</div>';

                            },
                            option_create: function(item, escape) {
                                return '<div class="create">Agregar <strong>' + escape(item.input) + '</strong>&hellip;</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;

                            // email@address.com
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);

                            // name <email@address.com>
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);

                            return false;
                        },
                        create: function(input) {
                            
                            if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                                idusernew = 0;
                                $.ajax({
                                    url: 'ajax.php/ccandcco/<?php echo $idTicketAssign; ?>/adduser',
                                    type: 'POST',
                                    async:false,
                                    data: { name: input, email:input },
                                    // dataType: 'json',
                                    error: function() {
                                        console.log('error');
                                    },
                                    success: function(res) {
                                        idusernew = res;
                                        
                                    }
                                }); 
                                return {email: input, id:idusernew}; 
                                
                            }
                            var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
                            if (match) {
                                return {
                                    email : match[2],
                                    name  : $.trim(match[1])
                                };
                            }
                            alert('Invalid email address.');
                            return false;
                        }
                    });
                    </script>
                </td>
            </tr>
                
            <tr id="tr-cco-reenviar" style="display:none;">
                <td width="120">
                    <label><strong><?php echo __('CCO'); ?>:</strong></label>
                </td>
                <td>
                <div style="
                    float:left;
                    border: 1px solid #d0d0d0;
                    padding: 10px 10px;
                    width: 100%;
                    background: #fff;
                    -webkit-box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    box-sizing: border-box;
                    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.1);
                    box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.1);
                    -webkit-border-radius: 3px;
                    -moz-border-radius: 3px;
                    border-radius: 3px;">
                <select id="cco-colaboradores-reenviar" name="cco[]" class="contacts" placeholder="Agregar" multiple style="width: 90%;">
                
                </select>
                </div>
                    <script>
                    var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
                  '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

                    $('#cco-colaboradores-reenviar').selectize({
                        persist: true,
                        maxItems: null,
                        valueField: 'id',
                        labelField: 'email',
                        plugins: ['remove_button'],
                        searchField: ['name', 'email','phone'],
                        options: [],
                        load: function(query, callback) {
                            if (!query.length) return callback();
                            $.ajax({
                                url: 'ajax.php/users/local?q=' + encodeURIComponent(query),
                                type: 'GET',
                                // async:false,
                                dataType: 'json',
                                error: function() {
                                    callback();
                                },
                                success: function(res) {
                                    console.log(res);
                                    callback(res);
                                }
                            });
                        },
                        render: {
                            item: function(item, escape) {
                                //console.log(item.name + '1');
                                return '<div>' +
                                    (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
                                '</div>';
                            },
                            option_create: function(item, escape) {
                                return '<div class="create">Agregar <strong>' + escape(item.input) + '</strong>&hellip;</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;

                            // email@address.com
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);

                            // name <email@address.com>
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);

                            return false;
                        },
                        create: function(input) {
                            
                            if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
                                idusernew = 0;
                                $.ajax({
                                    url: 'ajax.php/ccandcco/<?php echo $idTicketAssign; ?>/adduser',
                                    type: 'POST',
                                    async:false,
                                    data: { name: input, email:input },
                                    // dataType: 'json',
                                    error: function() {
                                        console.log('error');
                                    },
                                    success: function(res) {
                                        idusernew = res;
                                        
                                    }
                                }); 
                                return {email: input, id:idusernew}; 
                                
                            }
                            var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
                            if (match) {
                                return {
                                    email : match[2],
                                    name  : $.trim(match[1])
                                };
                            }
                            alert('Invalid email address.');
                            return false;
                        }
                    });

                    $('#span-cco-reenviar').click(function() {
                        if ($("#tr-cco-reenviar").css('display') == 'none') {
                            $("#tr-cco-reenviar").css('display','table-row');
                        }else{
                            $("#tr-cco-reenviar").css('display','none');
                        }
                        //console.log('open tr');
                    });
                    </script>
                </td>
             </tr>
            </tbody>
    </table>
    <hr>
        <p class="full-width">
            <span class="buttons pull-left">
                <input type="button" name="cancel" class="close"
                value="<?php echo __('Cancel'); ?>">
            </span>
            <span class="buttons pull-right">
                <input type="submit" value="Reenviar Ticket">
            </span>
         </p>
    </form>
</div>
<div class="clear"></div>
