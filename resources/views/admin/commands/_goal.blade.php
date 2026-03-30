<div id="goal-commands" class="mb-3">
    <div class="card" v-for="(command, i) in voteCommands">
        <div class="card-body">
            <div class="row gx-3">
                <div class="col-md-8">
                    <label class="form-label" :for="'commandInput' + i">{{ trans('vote::admin.goal.command') }}</label>

                    <div class="input-group mb-1" v-for="(cmd, j) in command.commands">
                        <input type="text" class="form-control" :id="'commandInput' + i" :name="`goal_commands[${i}][commands][${j}]`" v-model.trim="command.commands[j]" required>

                        <button type="button" v-if="j == command.commands.length - 1" class="btn btn-success" @click="command.commands.push('')" title="{{ trans('messages.actions.add') }}">
                            <i class="bi bi-plus-lg"></i>
                        </button>

                        <button type="button" v-if="command.commands.length > 1" class="btn btn-danger" @click="command.commands.splice(j, 1)" title="{{ trans('messages.actions.delete') }}">
                            <i class="bi bi-x-lg"></i>
                        </button>

                        <button type="button" v-else class="btn btn-danger" @click="voteCommands.splice(i, 1)" title="{{ trans('messages.actions.delete') }}">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label" :for="'serverSelect' + i">{{ trans('messages.fields.server') }}</label>

                    <select class="form-select" :id="'serverSelect' + i" :name="`goal_commands[${i}][server]`" v-model="command.server" required>
                        @foreach($servers as $serverId => $name)
                            <option value="{{ $serverId }}">
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                    @error('servers')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <button type="button" @click="voteCommands.push({ commands: [''], server: 0 })" class="btn btn-sm btn-success">
        <i class="bi bi-plus-lg"></i> {{ trans('messages.actions.add') }}
    </button>
</div>
