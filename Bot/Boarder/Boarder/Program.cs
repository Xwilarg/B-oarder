using Discord;
using Discord.WebSocket;
using Microsoft.Extensions.DependencyInjection;
using System.Diagnostics;
using System.Text.Json;

namespace Boarder.Bot
{
    public sealed class Program
    {
        private IServiceProvider _serviceProvider;

        private DiscordSocketClient _client;

        public static async Task Main()
        {
            try
            {
                await new Program().StartAsync();
            }
            catch (FileNotFoundException) // This probably means a dll is missing
            {
                throw;
            }
            catch (Exception e)
            {
                if (!Debugger.IsAttached)
                {
                    if (!Directory.Exists("Logs"))
                        Directory.CreateDirectory("Logs");
                    File.WriteAllText("Logs/Crash-" + DateTime.Now.ToString("yyyy-MM-dd-HH-mm-ss-ff") + ".txt", e.ToString());
                }
                else // If an exception occur, the program exit and is relaunched
                    throw;
            }
        }

        public async Task StartAsync()
        {
            _client = new(new()
            {
                LogLevel = LogSeverity.Info,
                GatewayIntents = GatewayIntents.Guilds | GatewayIntents.MessageContent | GatewayIntents.GuildMessages | GatewayIntents.DirectMessages
            });
            _client.Log += Log.LogAsync;

            await Log.LogAsync(new LogMessage(LogSeverity.Info, "Setup", "Initialising bot"));

            // Load credentials
            if (!File.Exists("Keys/credentials.json"))
                throw new FileNotFoundException("Missing Credentials file");
            var credentials = JsonSerializer.Deserialize<Credentials>(File.ReadAllText("Keys/credentials.json"))!;

            _serviceProvider = new ServiceCollection()
                .AddSingleton(_client)
                .BuildServiceProvider();

            _client.Ready += Ready;
            _client.SlashCommandExecuted += SlashCommandExecuted;

            await _client.LoginAsync(TokenType.Bot, credentials.BotToken);
            await _client.StartAsync();

            // We keep the bot online
            await Task.Delay(-1);
        }

        private async Task SlashCommandExecuted(SocketSlashCommand arg)
        {
            var cmd = arg.CommandName.ToUpperInvariant();

            if (cmd == "PING")
            {
                await arg.RespondAsync("Pong");
            }
        }

        private const ulong DebugGuildId = 1169565317920456705;
        private async Task Ready()
        {
            _ = Task.Run(async () =>
            {
                var cmds = new SlashCommandBuilder[]
                {
                   new()
                   {
                       Name = "ping",
                       Description = "Ping the bot"
                   }
                }.Select(x => x.Build()).ToArray();
                foreach (var cmd in cmds)
                {
                    if (Debugger.IsAttached)
                    {
                        await _client.GetGuild(DebugGuildId).CreateApplicationCommandAsync(cmd);
                    }
                    else
                    {
                        await _client.CreateGlobalApplicationCommandAsync(cmd);
                    }
                }
                if (Debugger.IsAttached)
                {
                    await _client.GetGuild(DebugGuildId).BulkOverwriteApplicationCommandAsync(cmds);
                }
                else
                {
                    await _client.GetGuild(DebugGuildId).DeleteApplicationCommandsAsync();
                    await _client.BulkOverwriteGlobalApplicationCommandsAsync(cmds);
                }
            });
        }
    }
}