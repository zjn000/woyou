define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'coupon/coupon/index',
                    add_url: 'coupon/coupon/add',
                    edit_url: 'coupon/coupon/edit',
                    del_url: '',
                    multi_url: 'coupon/coupon/multi',
                    table: 'coupon',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'name', title: __('Name'),operate: 'LIKE'},
                        {field: 'face_value', title: __('Face Value'),operate: false},
                        {field: 'type_name', title: __('Type'),operate: false},
                        {field: 'effective_time', title: __('Effective_time'),operate: false},
                        {field: 'status', title: __('Status'),operate: false, formatter: Controller.api.formatter.status},
                        {field: 'admin_name', title: __('Update_id'),operate: false},
                        {field: 'update_time', title: __('Update_time'),operate: false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [{
                                name: 'send',
                                text: __('Push Coupon'),
                                icon: 'fa fa-send',
                                classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                url: 'coupon/coupon/push'
                            }],
                            formatter: Table.api.formatter.operate}
                    ]
                ],
                search:false

            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            var row1 = document.getElementById('row[type]-1');
            var row2 = document.getElementById('row[type]-2');
            var start = document.getElementById('c-start_time');
            var end = document.getElementById('c-end_time');
            var vd = document.getElementById('c-valid_date');

            var f = function () {
                if (row1.checked) {
                    $('#group1').show()
                    $('#group2').hide()
                    start.disabled =true;
                    end.disabled =true;
                    vd.disabled=false;
                }
                if (row2.checked) {
                    $('#group1').hide()
                    $('#group2').show()
                    start.disabled =false;
                    end.disabled =false;
                    vd.disabled=true;
                }
            };
            f();
            $('input[type=radio]').on('click',function () {f()})
            Controller.api.bindevent();
        },
        edit: function () {
            var row1 = document.getElementById('row[type]-1');
            var row2 = document.getElementById('row[type]-2');
            var start = document.getElementById('c-start_time');
            var end = document.getElementById('c-end_time');
            var vd = document.getElementById('c-valid_date');

            var f = function () {
                if (row1.checked) {
                    $('#group1').show()
                    $('#group2').hide()
                    start.disabled =true;
                    end.disabled =true;
                    vd.disabled=false;
                }
                if (row2.checked) {
                    $('#group1').hide()
                    $('#group2').show()
                    start.disabled =false;
                    end.disabled =false;
                    vd.disabled=true;
                }
            };
            f();
            $('input[type=radio]').on('click',function () {f()})
            Controller.api.bindevent();
        },
        push: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {

                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                status: function (value, row, index) {

                    var color = '';
                    var status_text = '';
                    if(row.status == 1){
                        color = 'success';
                        status_text = __('Effective');
                    }
                    if(row.status == 0){
                        color = 'danger'
                        status_text = __('Invalid');
                    }
                    //渲染状态
                    var html = '<span class="text-' + color + '"><i class="fa fa-circle"></i>'+status_text+'</span>';
                    return html;

                }
            }
        }
    };
    return Controller;
});